<?php

use Phalcon\Mvc\Controller;


/**
 * Class BaseController
 */
class BaseController extends Controller
{

    const API_STATUS_ERROR = 'error';
    const API_STATUS_OK = 'success';


    private $errorResponse = ['status' => self::API_STATUS_ERROR, 'reason' => ''];
    private $successResponse = ['status' => self::API_STATUS_ERROR, 'data' => ''];


    /**
     * @return \Phalcon\Http\Response
     */
    private function initResponse()
    {
        $response = new \Phalcon\Http\Response();
        $response->setHeader('Cache-Control', 'private, max-age=0, must-revalidate');
        
        return $response;
    }

    /**
     * Error response
     * @param $reason
     * @return \Phalcon\Http\Response
     */
    protected function responseError($reason)
    {
        $response = $this->initResponse();
        $this->errorResponse['reason'] = $reason;
        $response->setJsonContent($this->errorResponse);

        return $response;
    }

    /**
     * Success response
     * @param $content
     * @return \Phalcon\Http\Response
     */
    protected function responseSuccess($content)
    {
        $response = $this->initResponse();
        $this->successResponse['data'] = $content;
        $response->setJsonContent($this->successResponse);

        return $response;

    }

    /**
     * Wrong http method response
     */
    protected function responseWrongMethod() {
        return $this->responseError('Unsupported HTTP method.');
    }

}