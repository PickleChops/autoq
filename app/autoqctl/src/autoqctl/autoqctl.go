// Boyd Stratton 2016

//autoctl client
package main

import (
	"bytes"
	"fmt"
	"gopkg.in/alecthomas/kingpin.v2"
	"io"
	"io/ioutil"
	"log"
	"net/http"
	"os"
)

const (
	apiHost = "http://autoq.localdev"
)

var (
	name       = kingpin.Flag("name", "The name of your job.").Short('d').String()
	connection = kingpin.Flag("connection", "The name of the connection you want to use for your job.").Short('c').Default("default").String()
	schedule   = kingpin.Flag("schedule", "When you would like to run a job.").Short('s').Default("ASAP").String()
	query      = kingpin.Flag("query", "The query that your job will run.").Short('q').PlaceHolder("\" <your SQL query> \"").String()
	email      = kingpin.Flag("email", "The email address that will receive the query results.").Short('e').String()
	file       = kingpin.Flag("file", "A YAML job definiton file.").Short('f').String()
)

type Output struct {
}

type JobDefinition struct {
}

var out io.Writer = os.Stdout

var logger *log.Logger

// Off we go
func main() {

	//Kick off a logger
	logger = log.New(out, "", log.Ldate|log.Ltime)

	//Parse the command line flags provided
	kingpin.Parse()

	logger.Println("Autoqctl started...")

	if *file != "" {

		logger.Printf("Reading Autoq job definition file: %s", *file)

		fileContents, err := ioutil.ReadFile(*file)

		if err != nil {
			logger.Fatalf(fmt.Sprintf("autoqctl: %v\n", err))
		}

		resp, err := postToAutoq(fileContents)

		fmt.Println("function response:", resp)

	} else {
		if *name == "" {
			logger.Fatalf("You must give your job a name!")
		} else if *query == "" {
			logger.Fatalf("You must specify a query to run!")
		} else if *email == "" {
			logger.Fatalf("You must specify an email address!")
		} else {
			logger.Printf("You are posting a job to Autoq: name \"" + *name + "\" on connection " + *connection + " with schedule " + *schedule + " that will be delivered to email " + *email)
			postBody := buildPostBodyFromFlags(*name, *connection, *schedule, *query, *email)
			resp, _ := postToAutoq(postBody)
			fmt.Println("function response:", resp)
		}
	}

}

//Build our basic post payload
func buildPostBodyFromFlags(name string, connection string, schedule string, query string, email string) []byte {

	bodyString := fmt.Sprintf("name: %s\nconnection: %s\n", name, connection)

	return []byte(bodyString)
}

//Make server request
func apiGetRequest(requestUri string) string {

	//Simple fetch from api
	resp, err := http.Get("http://autoq.localdev/" + requestUri)

	if err != nil {
		logger.Fatalf(fmt.Sprintf("autoqctl: %v\n", err))
	}

	defer resp.Body.Close()

	content, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		logger.Fatal(err)
	}

	return string(content)
}

// Post contents to /jobs/ on Autoq
func postToAutoq(postBody []byte) (string, error) {

	url := apiHost + "/jobs/"

	req, err := http.NewRequest("POST", url, bytes.NewBuffer(postBody))

	client := http.Client{}
	resp, err := client.Do(req)

	defer resp.Body.Close()

	if err != nil {
		logger.Fatalf(fmt.Sprintf("autoqctl: %v\n", err))
	}

	fmt.Println("response Code:", resp.StatusCode)

	body, err := ioutil.ReadAll(resp.Body)

	if err != nil {
		logger.Fatalf(fmt.Sprintf("autoqctl: %v\n", err))
	}

	return string(body), err

}
