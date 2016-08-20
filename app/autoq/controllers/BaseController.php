<?php

namespace Autoq\Controllers;

use Autoq\Data\BaseRepository;
use Autoq\Services\ApiHelper;
use Autoq\Services\DbConnectionMgr;
use Phalcon\Dispatcher;
use Phalcon\Mvc\Controller;


/**
 * Class BaseController
 */
class BaseController extends Controller
{
    /**
     * @var $apiHelper \Autoq\Services\ApiHelper
     */
    protected $apiHelper;

    /**
     * Standard api response for no entity
     * @param $entity
     * @param $entityID
     * @return \Phalcon\Http\Response
     */
    protected function apiEntityNotFound($entity, $entityID)
    {
        return $this->apiHelper->responseError("$entity with ID: $entityID does not exist");
    }
}