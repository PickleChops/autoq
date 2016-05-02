<?php

class Error404Controller extends BaseController
{
    public function indexAction()
    {
        /**
         * @var $apiHelper \Api\Services\ApiHelper
         */
        $apiHelper = $this->di->get('apiHelper');

        return $apiHelper->response404();
    }
}