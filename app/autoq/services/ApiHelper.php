<?php

namespace Api\Services;


use Phalcon\Http\Response;

class ApiHelper
{

    const API_STATUS_ERROR = 'error';
    const API_STATUS_OK = 'success';


    private $errorResponse = ['status' => self::API_STATUS_ERROR, 'reason' => ''];
    private $successResponse = ['status' => self::API_STATUS_OK];


    /**
     * @return \Phalcon\Http\Response
     */
    private function initResponse()
    {
        $response = new Response();
        $response->setHeader('Cache-Control', 'private, max-age=0, must-revalidate');
        $response->setHeader('Content-Type','application/json');

        return $response;
    }

    /**
     * Error response
     * @param $reason
     * @return \Phalcon\Http\Response
     */
    public function responseError($reason)
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
    public function responseSuccessWithData($content)
    {
        $response = $this->initResponse();
        
        $this->successResponse['data'] = $content;
        $response->setJsonContent($this->successResponse);

        return $response;

    }

    /**
     * Success response
     * @param $content
     * @return \Phalcon\Http\Response
     */
    public function responseSuccess()
    {
        $response = $this->initResponse();
        
        $response->setJsonContent($this->successResponse);

        return $response;

    }

    /**
     * Wrong http method response
     */
    public function responseWrongMethod()
    {
        return $this->responseError('Unsupported HTTP method.');
    }


    /**
     * 404 response
     * @return Response
     */
    public function response404()
    {
        $response = $this->initResponse();
        $this->errorResponse['reason'] = "Requested url not found";
        $response->setJsonContent($this->errorResponse);
        $response->setStatusCode(404);
        return $response;
    }

}