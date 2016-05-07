<?php


/**
 * Class QueueController Endpoints for /queue/ api calls
 */
class QueueController extends BaseController
{
    /**
     * @var $apiHelper \Autoq\Services\ApiHelper
     */
    private $apiHelper;

    /**
     * @var $repo \Autoq\Data\Queue\QueueRepository
     */
    private $repo;


    /**
     * Run on contruction by Phalcon
     */
    public function initialize()
    {
        $this->apiHelper = $this->di->get('apiHelper');
        $this->repo = $this->di->get(\Autoq\Data\Queue\QueueRepository::class, [$this->getDI()]);

    }

    /**
     * Fetch an existing job
     * @param $jobID
     * @return \Phalcon\Http\Response
     */
    public function getAction($jobID)
    {
        if (($job = $this->repo->getByID($jobID)) === false) {
            $response = $this->apiHelper->responseError("Unable to read job");
        } elseif($job === []) {
            $response = $this->apiHelper->responseError("Job with ID: $jobID does not exist");

        } else {
            $response = $this->apiHelper->responseSuccessWithData($job);
        }
        return $response;
    }

    /**
     * Fetch all queue items
     * @return \Phalcon\Http\Response
     */
    public function getAllAction()
    {
    
        $limit = $this->request->getQuery('limit','int', null);
        

        if (($queueItems = $this->repo->getAll($limit)) === false) {
            $response = $this->apiHelper->responseError("Unable to read queue items");
        } else {
            $response = $this->apiHelper->responseSuccessWithData($queueItems);
        }
        return $response;
    }
    
}