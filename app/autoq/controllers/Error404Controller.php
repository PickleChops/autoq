<?php

namespace Autoq\Controllers;

class Error404Controller extends BaseController
{
    public function indexAction()
    {
        /**
         * @var $apiHelper \Autoq\Services\ApiHelper
         */
        $apiHelper = $this->di->get('apiHelper');

        return $apiHelper->response404();
    }
}