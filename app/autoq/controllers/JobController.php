<?php

namespace Autoq\Controllers;

use Autoq\Data\Jobs\JobsRepository;

/**
 * Class JobController Endpoints for /job/ api calls
 */
class JobController extends BaseController
{

    /**
     * Run on contruction by Phalcon
     */
    public function initialize()
    {
        //Indicate the repo to use for this contoller
        parent::initialize(JobsRepository::class);
    }

    /**
     * Expects a POST request with details of a new job submission
     * @return \Phalcon\Http\Response
     */
    public function addAction()
    {
        if ($this->request->isPost()) {

            /**
             * @var $jobValidator \Autoq\Services\JobProcessor\JobDefinitionProcessor
             */
            $jobValidator = $this->di->get('jobProcessor');

            $rawDefinition = $this->request->getRawBody();

            //Run some basic checks on the defintion
            if ($jobValidator->processJobDefiniton($rawDefinition)) {

                if (($jobID = $this->repo->save($jobValidator->getValidatedDefinition())) === false) {
                    $response = $this->apiHelper->responseError("Unable to add job");
                } else {

                    if (($jobDefinition = $this->repo->getById($jobID)) === false) {
                        $response = $this->apiHelper->responseError("Job added but unable to read!");
                    } else {
                        $response = $this->apiHelper->responseSuccessWithData($jobDefinition);
                    }
                }

            } else {
                //Send back validation error
                $response = $this->apiHelper->responseError($jobValidator->getFirstError());
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
        if (($jobDefinition = $this->repo->getById($jobID)) === false) {
            $response = $this->apiHelper->responseError("Unable to read job");
        } elseif ($jobDefinition === []) {
            $response = $this->apiEntityNotFound("Job", $jobID);

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
             * @var $jobProcessor \Autoq\Services\JobProcessor\JobDefinitionProcessor
             */
            $jobProcessor = $this->di->get('jobProcessor');

            $rawDefinition = $this->request->getRawBody();

            //Run some basic checks on the defintion
            if ($jobProcessor->processJobDefiniton($rawDefinition)) {

                if ($this->repo->exists($jobID)) {

                    if ($this->repo->update($jobID, $jobProcessor->getValidatedDefinition()) === false) {
                        $response = $this->apiHelper->responseError("Unable to update job: $jobID");
                    } else {

                        if (($jobDefinition = $this->repo->getById($jobID)) === false) {
                            $response = $this->apiHelper->responseError("Job updated but unable to read!");
                        } else {
                            $response = $this->apiHelper->responseSuccessWithData($jobDefinition);
                        }
                    }
                } else {
                    $response = $this->apiEntityNotFound("Job", $jobID);
                }

            } else {
                //Send back validation error
                $response = $this->apiHelper->responseError($jobProcessor->getFirstError());
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
        if ($this->repo->exists($jobID)) {

            if ($this->repo->delete($jobID) === false) {
                $response = $this->apiHelper->responseError("Unable to delete job: $jobID");
            } else {
                $response = $this->apiHelper->responseSuccess();
            }
        } else {
            $response = $this->apiEntityNotFound("Job", $jobID);
        }

        return $response;
    }
}