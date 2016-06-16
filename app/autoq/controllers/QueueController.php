<?php

namespace Autoq\Controllers;
use Autoq\Data\Queue\QueueControl;

/**
 * Class QueueController Endpoints for /queue/ api calls
 */
class QueueController extends BaseController
{

    /**
     * @var $repo QueueControl
     */
    protected $repo;

    /**
     * Run on contruction by Phalcon
     */
    protected function initialize()
    {
        $this->apiHelper = $this->di->get('apiHelper');
        $this->repo = $this->di->get(QueueControl::class, [$this->getDI()]);
    }

    /**
     * Fetch an existing job
     * @param $jobID
     * @return \Phalcon\Http\Response
     */
    public function getAction($jobID)
    {
        if (($job = $this->repo->getById($jobID)) === false) {
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