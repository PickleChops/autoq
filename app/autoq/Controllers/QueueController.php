<?php

namespace Autoq\Controllers;
use Autoq\Data\Queue\QueueControl;

/**
 * Class QueueController Endpoints for /queue/ api calls
 */
class QueueController extends SecureController
{
    /**
     * @var $queueControl QueueControl
     */
    protected $queueControl;

    /**
     * Run on contruction by Phalcon
     */
    protected function initialize()
    {
        $this->apiHelper = $this->di->get('apiHelper');
        $this->queueControl = $this->di->get('queueControl');
    }

    /**
     * Fetch item from queue
     * @param $queueItemID
     * @return \Phalcon\Http\Response
     */
    public function getAction($queueItemID)
    {
        if (($queueItem = $this->queueControl->getById($queueItemID)) === false) {
            $response = $this->apiHelper->responseError("Unable to read queue Item: $queueItemID");
        } elseif($queueItem === []) {
            $response = $this->apiHelper->responseError("Queue item with ID: $queueItemID does not exist");

        } else {
            $response = $this->apiHelper->responseSuccessWithData($queueItem);
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
        
        if (($queueItems = $this->queueControl->getAll($limit)) === false) {
            $response = $this->apiHelper->responseError("Unable to read queue items");
        } else {
            $response = $this->apiHelper->responseSuccessWithData($queueItems);
        }
        return $response;
    }
    
}