<?php

namespace Autoq\Controllers;

use Autoq\Data\BaseRepository;
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
     * @var $repo BaseRepository
     */
    protected $repo;

    /**
     * Run on contruction by Phalcon
     * @param $repositoryClass
     */
    protected function initialize($repositoryClass)
    {
        $this->apiHelper = $this->di->get('apiHelper');
        $this->repo = $this->di->get($repositoryClass, [$this->getDI()]);
    }

    /**
     * Standard api response for no entity
     * @param $entity
     * @param $entityID
     * @return \Phalcon\Http\Response
     */
    protected function apiEntityNotFound($entity, $entityID) {

        return $this->apiHelper->responseError("$entity with ID: $entityID does not exist");
        
    }
    
}