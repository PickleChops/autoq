<?php

use Phalcon\Mvc\Controller;

/**
 * Class JobController
 *
 * Endpoints for /job/ api calls
 *
 */

class JobController extends BaseController
{

    /**
     * Expects a POST request with details of a new job submission
     * @return \Phalcon\Http\Response
     */
    public function addAction()
    {

        if ($this->request->isPost()) {

            //Validate input

            //If good add to db and return response in format

            //if fail construct standard error response

            $response = $this->responseSuccess(['id' => 99]);

        } else {
            $response = $this->responseWrongMethod();
        }


        return $response;
    }

    public function statusAction()
    {

    }

    public function cancelAction()
    {

    }

}