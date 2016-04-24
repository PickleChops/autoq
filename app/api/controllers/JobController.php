<?php


/**
 * Class JobController Endpoints for /job/ api calls
 */
class JobController extends BaseController
{
    private $debugJobAdd = false;

    /**
     * Expects a POST request with details of a new job submission
     * @return \Phalcon\Http\Response
     */
    public function addAction()
    {

        /**
         * @var $apiHelper \Api\Services\ApiHelper
         */
        $apiHelper = $this->di->get('apiHelper');;

        if ($this->request->isPost() || $this->checkForDebugOverride()) {

            /**
             * @var $jobValidator \Api\Services\ValidateJobDefintion
             */
            $jobValidator = $this->di->get('jobValidator');

            //The Yaml definition is in the post body or provided by debug
            $rawDefinition = $this->debugJobAdd ? $this->debugJobAdd : $this->request->getRawBody();

            //Run some basic checks on the defintion
            if ($jobValidator->validateDefiniton($rawDefinition)) {

                //Save the defintion

                $jm = new JobModel($this->di);


                $response = $apiHelper->responseSuccess('hello');

            } else {

                //Send back errors in job submission
                $response = $apiHelper->responseError($jobValidator->getErrorMsg());

            }

        } else {
            $response = $apiHelper->responseWrongMethod();
        }


        return $response;
    }

    public function statusAction()
    {

    }

    public function cancelAction()
    {

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