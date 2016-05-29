<?php

namespace Autoq\Controllers;
use Autoq\Data\Jobs\JobsRepository;

/**
 * Class JobController Endpoints for /job/ api calls
 */
class JobController extends BaseController
{
    private $debugJobAdd = false;

    /**
     * @var $apiHelper \Autoq\Services\ApiHelper
     */
    private $apiHelper;

    /**
     * @var $repo JobsRepository
     */
    private $repo;


    /**
     * Run on contruction by Phalcon
     */
    public function initialize()
    {
        $this->apiHelper = $this->di->get('apiHelper');
        $this->repo = $this->di->get(JobsRepository::class, [$this->getDI()]);
    }

    /**
     * Expects a POST request with details of a new job submission
     * @return \Phalcon\Http\Response
     */
    public function addAction()
    {
        if ($this->request->isPost() || $this->checkForDebugOverride()) {

            /**
             * @var $jobValidator \Autoq\Services\ValidateJobDefintion
             */
            $jobValidator = $this->di->get('jobValidator');

            $rawDefinition = $this->getPostBody();

            //Run some basic checks on the defintion
            if ($jobValidator->validateDefiniton($rawDefinition)) {

                if (($jobID = $this->repo->save($jobValidator->getDefAsYaml())) === false) {
                    $response = $this->apiHelper->responseError("Unable to add job");
                } else {

                    if (($jobDefinition = $this->repo->getByID($jobID)) === false) {
                        $response = $this->apiHelper->responseError("Job added but unable to read!");
                    } else {
                        $response = $this->apiHelper->responseSuccessWithData($jobDefinition);
                    }
                }

            } else {
                //Send back validation error
                $response = $this->apiHelper->responseError($jobValidator->getErrorMsg());
            }

        } else {
            $response = $this->apiHelper->responseWrongMethod();
        }

        return $response;
    }

    /**
     * Fetch an existing job
     * @param $jobID
     * @return \Phalcon\Http\Response
     */
    public function getAction($jobID)
    {
        if (($jobDefinition = $this->repo->getByID($jobID)) === false) {
            $response = $this->apiHelper->responseError("Unable to read job");
        } elseif($jobDefinition === []) {
            $response = $this->apiHelper->responseError("Job with ID: $jobID does not exist");

        } else {
            $response = $this->apiHelper->responseSuccessWithData($jobDefinition);
        }
        return $response;
    }

    /**
     * Fetch all jobs
     * @return \Phalcon\Http\Response
     */
    public function getAllAction()
    {
        if (($jobs = $this->repo->getAll()) === false) {
            $response = $this->apiHelper->responseError("Unable to read jobs");
        } else {
            $response = $this->apiHelper->responseSuccessWithData($jobs);
        }
        return $response;
    }

    /**
     * Update an existing job
     * @param $jobID
     * @return \Phalcon\Http\Response
     */
    public function putAction($jobID)
    {

        if ($this->request->isPut()) {

            /**
             * @var $jobValidator \Autoq\Services\ValidateJobDefintion
             */
            $jobValidator = $this->di->get('jobValidator');

            $rawDefinition = $this->getPostBody(); 

            //Run some basic checks on the defintion
            if ($jobValidator->validateDefiniton($rawDefinition)) {

                if ($this->repo->exists($jobID)) {

                    if ($this->repo->update($jobID, $jobValidator->getDefAsYaml()) === false) {
                        $response = $this->apiHelper->responseError("Unable to update job: $jobID");
                    } else {

                        if (($jobDefinition = $this->repo->getByID($jobID)) === false) {
                            $response = $this->apiHelper->responseError("Job updated but unable to read!");
                        } else {
                            $response = $this->apiHelper->responseSuccessWithData($jobDefinition);
                        }
                    }
                } else {
                    $response = $this->apiHelper->responseError("There is no job with id: $jobID");
                }

            } else {
                //Send back validation error
                $response = $this->apiHelper->responseError($jobValidator->getErrorMsg());
            }

        } else {
            $response = $this->apiHelper->responseWrongMethod();
        }

        return $response;
    }


    /**
     * Delete an existing job definition
     * @param $jobID
     * @return \Phalcon\Http\Response
     */
    public function deleteAction($jobID)
    {
        if ($this->repo->delete($jobID) === false) {
            $response = $this->apiHelper->responseError("Unable to delete job: $jobID");
        } else {
            $response = $this->apiHelper->responseSuccess();
        }
        
        return $response;
    }

    /**
     * Fetch the Yaml defintion
     * @return bool|string
     */
    private function getPostBody()
    {
        //The Yaml definition is in the post body or provided by debug
        return $this->debugJobAdd ? $this->debugJobAdd : $this->request->getRawBody();
    }

    /**
     * Debug function allow request without POST
     * @return bool
     */
    private function checkForDebugOverride()
    {
        if (getenv('DEBUG_OVERRIDE_JOB_ADD_POST')) {

            $this->debugJobAdd = <<<YAML
name: Sample job 
connection: default
schedule: Every Tuesday at 9am
query: |
    Select * from massive_table
    where some_condition is true
    group by 1
outputs:
  - type: s3
    bucket: an_s3_bucket
    format: csv
  - type: email
    address: boydi@boydi.com
    format: html
YAML;

            return true;
        }

        return false;
    }

}