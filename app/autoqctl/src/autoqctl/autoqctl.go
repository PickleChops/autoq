// Boyd Stratton 2016

//autoctl client
package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"gopkg.in/alecthomas/kingpin.v2"
	"io"
	"io/ioutil"
	"log"
	"net/http"
	"os"
	"os/user"
	"text/template"
	"time"
)

const (
	apiHost    = "http://autoq.localdev"
	apiError   = "error"
	apiSuccess = "success"
)

//Templates

// /job/ POST success
const addTemplSuccess = `
Status:   {{.Status}}
Job Name: {{.Data.Name}}
Job ID:   {{.Data.Id}}
Schedule: {{.Data.Schedule}}

`

// api error
const apiErrorTempl = `
Status:   {{.Status}}
Reason:   {{.Reason}}

`

//Queue status

const queueStatusTempl = `
Latest work queue status
------------------------

{{range .Data}}
Queue Id    : {{.Id}}
Job Name    : {{.Job_def.Name}}
Status      : {{.Flow_control.Status}}
Status Time : {{.Flow_control.Status_time | convertUnixTime}}
{{end}}

`

var (
	app = kingpin.New("autoqctl", "A command line client for Autoq")

	jobs           = app.Command("jobs", "Work with job definitions")
	jobsFile       = jobs.Flag("file", "A YAML job definiton file.").Short('f').String()
	jobsName       = jobs.Flag("name", "The name of your job.").Short('d').String()
	jobsConnection = jobs.Flag("connection", "The name of the connection you want to use for your job.").Short('c').Default("default").String()
	jobsSchedule   = jobs.Flag("schedule", "When you would like to run a job.").Short('s').Default("ASAP").String()
	jobsQuery      = jobs.Flag("query", "The query that your job will run.").Short('q').PlaceHolder("\" <your SQL query> \"").String()
	jobsEmail      = jobs.Flag("email", "The email address that will receive the query results.").Short('e').String()

	status = app.Command("status", "View status of work queue")
)

type AppConfig struct {
	Host   string
	Apikey string
}

type QueueApiResponse struct {
	Status string
	Reason string
	Data   []QueueApiResponseData
}

type QueueApiResponseData struct {
	Id             int
	Created        string
	Updated        string
	Data_stage_key string
	Flow_control   QueueFlowControl
	Job_def        JobApiResponseData
}

type QueueFlowControl struct {
	Status         string
	Status_time    int
	Status_history QueueStatus
}

type QueueStatus struct {
	NEW               int
	FETCHING          int
	FETCHING_COMPLETE int
	SENDING           int
	COMPLETED         int
	ERROR             int
	ABORTED           int
}

type JobApiResponse struct {
	Status string
	Reason string
	Data   JobApiResponseData
}

type JobApiResponseData struct {
	Id         int
	Name       string
	Query      string
	Created    string
	Updated    string
	Schedule   string
	Connection string
	Outputs    []JobOutput
}

type ApiStatusResponse struct {
	Status string
}

type JobOutput struct {
	Type    string
	Address string
	Style   string
}

var out io.Writer = os.Stdout

var logger *log.Logger

var config AppConfig

// Off we go
func main() {

	//Kick off a logger
	logger = log.New(out, "", log.Ldate|log.Ltime)

	logger.Println("Autoqctl started")

	readConfigFile()

	switch kingpin.MustParse(app.Parse(os.Args[1:])) {

	case jobs.FullCommand():
		jobsCommand()

	case status.FullCommand():
		statusCommand()
	}
}

// Look for config file holding details of host/api key
func readConfigFile() {

	usr, err := user.Current()
	if err != nil {
		log.Fatal(err)
	}

	logger.Println("Reading config file...")

	fileContents, err := ioutil.ReadFile(usr.HomeDir + "/.autoq")

	if err != nil {
		logger.Fatalf(fmt.Sprintf("autoqctl: %v\n", err))
	}

	if err := json.Unmarshal(fileContents, &config); err != nil {
		logger.Fatalf("Problem reading config: %s", err)
	}

	logger.Println("Using Autoq host: " + config.Host)

}

//Process status command
func statusCommand() {

	resp := apiGetRequest("/queue/")

	apiResp := unmarshallQueueResponse(resp)

	displayQueueResponse(apiResp)

}

//Process jobs related commands
func jobsCommand() {

	if *jobsFile != "" {

		logger.Printf("Reading Autoq job definition file: %s", *jobsFile)

		fileContents, err := ioutil.ReadFile(*jobsFile)

		if err != nil {
			logger.Fatalf(fmt.Sprintf("autoqctl: %v\n", err))
		}

		resp, _ := postJobDefinition(fileContents)

		apiResp := unmarshallJobResponse(resp)

		displayAddResponse(apiResp)

	} else {
		if *jobsName == "" {
			logger.Fatalf("You must give your job a name!")
		} else if *jobsQuery == "" {
			logger.Fatalf("You must specify a query to run!")
		} else if *jobsEmail == "" {
			logger.Fatalf("You must specify an email address!")
		} else {
			logger.Printf("You are posting a job to Autoq: name \"" + *jobsName + "\" on connection " + *jobsConnection + " with schedule " + *jobsSchedule + " that will be delivered to email " + *jobsEmail)

			postBody := buildPostBodyFromFlags(*jobsName, *jobsConnection, *jobsSchedule, *jobsQuery, *jobsEmail)

			resp, _ := postJobDefinition(postBody)

			apiResp := unmarshallJobResponse(resp)

			displayAddResponse(apiResp)

		}
	}
}

//Return unmarshalled API response for job related calls
func unmarshallJobResponse(resp []byte) JobApiResponse {

	var apiResp JobApiResponse

	if err := json.Unmarshal(resp, &apiResp); err != nil {
		logger.Fatalf("Problem reaading api response: %s", err)
	}

	if apiResp.Status != apiError && apiResp.Status != apiSuccess {
		logger.Fatalf("Unknown status response: %s", apiResp.Status)
	}

	return apiResp

}

//Display results from adding a job
func displayQueueResponse(apiResp QueueApiResponse) {

	if apiResp.Status == apiSuccess {

		var display = template.Must(
			template.New("QueueStatusSuccess").Funcs(template.FuncMap{"convertUnixTime": convertUnixTime}).Parse(queueStatusTempl))

		if err := display.Execute(os.Stdout, apiResp); err != nil {
			log.Fatal(err)
		}

	} else {
		var display = template.Must(template.New("ApiError").Parse(apiErrorTempl))

		if err := display.Execute(os.Stdout, apiResp); err != nil {
			log.Fatal(err)
		}
	}

}

//Display results from adding a job
func displayAddResponse(apiResp JobApiResponse) {

	if apiResp.Status == apiSuccess {

		var display = template.Must(template.New("JobAddSuccess").Parse(addTemplSuccess))

		if err := display.Execute(os.Stdout, apiResp); err != nil {
			log.Fatal(err)
		}

	} else {
		var display = template.Must(template.New("ApiError").Parse(apiErrorTempl))

		if err := display.Execute(os.Stdout, apiResp); err != nil {
			log.Fatal(err)
		}
	}

}

//Return unmarshalled API response for job related calls
func unmarshallQueueResponse(resp []byte) QueueApiResponse {

	var apiResp QueueApiResponse

	if err := json.Unmarshal(resp, &apiResp); err != nil {
		logger.Fatalf("Problem reaading api response: %s", err)
	}

	if apiResp.Status != apiError && apiResp.Status != apiSuccess {
		logger.Fatalf("Unknown status response: %s", apiResp.Status)
	}

	return apiResp

}

//Get the status we received back from an Api call
func getApiStatus(resp []byte) string {

	var apiError ApiStatusResponse

	if err := json.Unmarshal(resp, &apiError); err != nil {
		logger.Fatalf("Problem reaading api response: %s", err)
	}

	return apiError.Status
}

//Build our basic post payload
func buildPostBodyFromFlags(name string, connection string, schedule string, query string, email string) []byte {

	bodyString := fmt.Sprintf("name: %s\nconnection: %s\nschedule: %s\nquery: %s\noutputs:\n  - type: email\n    address: %s\n", name, connection, schedule, query, email)

	return []byte(bodyString)
}

//Make server request
func apiGetRequest(requestPath string) []byte {

	url := "http://" + config.Host + requestPath + "?apikey=" + config.Apikey

	//Simple fetch from api
	resp, err := http.Get(url)

	if err != nil {
		logger.Fatalf(fmt.Sprintf("autoqctl: %v\n", err))
	}

	defer resp.Body.Close()

	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		logger.Fatal(err)
	}

	return body
}

// Post contents to /jobs/ on Autoq
func postJobDefinition(postBody []byte) ([]byte, error) {

	url := "http://" + config.Host + "/jobs/" + "?apikey=" + config.Apikey

	req, err := http.NewRequest("POST", url, bytes.NewBuffer(postBody))

	client := http.Client{}
	resp, err := client.Do(req)

	defer resp.Body.Close()

	if err != nil {
		logger.Fatalf(fmt.Sprintf("autoqctl: %v\n", err))
	}

	body, err := ioutil.ReadAll(resp.Body)

	if err != nil {
		logger.Fatalf(fmt.Sprintf("autoqctl: %v\n", err))
	}

	return body, err

}

//Convert a unixtime stamp
func convertUnixTime(timeInt int) time.Time {

	i := int64(timeInt)
	return time.Unix(i, 0)

}
