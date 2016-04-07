// Boyd Stratton 2016

//autoctl client
package main

import (
	"fmt"
	"io"
	"os"
	"log"
	"net/http"
	"io/ioutil"
	"gopkg.in/alecthomas/kingpin.v2"
)


var (
	schedule = kingpin.Flag("schedule", "When you would like to run a job").Short('s').Default("asap").String()
	query = kingpin.Flag("query", "The query that your job will run").Short('q').PlaceHolder("\"<some SQL>\"").String()
	output = kingpin.Flag("output", "Where you like the results of your query to go").Short('o').Default("email").Enum("email","s3")
)

var out io.Writer = os.Stdout // modified during testing

var logger *log.Logger

func main() {

	//Kick off a logger
	logger = log.New(out, "", log.Ldate | log.Ltime)

	//Parse the command line flags provided
	kingpin.Parse()

	logger.Println("Sending request to server...")
	response := apiGetRequest("api/sayhello/");

	logger.Printf("Server says: %s", response);

}

//Make server request
func apiGetRequest(requestUri string) (string) {

	//Simple fetch from api
	resp, err := http.Get("http://autoq.localdev/" + requestUri)

	if err != nil {
		log.Fatalf(fmt.Sprintf("autoqctl: %v\n", err))
	}

	defer resp.Body.Close()

	content, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		log.Fatal(err)
	}

	return string(content)
}





