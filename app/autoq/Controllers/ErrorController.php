<?php

namespace Autoq\Controllers;

class ErrorController extends BaseController
{
    public function notFoundAction()
    {
        /**
         * @var $apiHelper \Autoq\Services\ApiHelper
         */
        $apiHelper = $this->di->get('apiHelper');

        return $apiHelper->response404();
    }


    /**
     * Useful for other controller to forward an error response on to
     * @param $response
     * @return mixed
     */
    public function responseAction($response) {
        return $response;
    }
}