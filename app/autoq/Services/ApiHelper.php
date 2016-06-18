<?php

namespace Autoq\Services;


use Autoq\Data\Arrayable;
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
     * Success with data
     * @param $content
     * @return Response
     * @throws \Exception
     */
    public function responseSuccessWithData($content)
    {
        $responseContent = [];

        if(is_array($content)) {

            foreach($content as $arrayItem) {
                $responseContent[] = $this->convertObjectToArray($arrayItem);
            }

        } else {
            $responseContent = $this->convertObjectToArray($content);
        }

        $response = $this->initResponse();
        $this->successResponse['data'] = $responseContent;
        $response->setJsonContent($this->successResponse);

        return $response;

    }

    /**
     * @param $obj
     * @return mixed
     * @throws \Exception
     */
    private function convertObjectToArray($obj) {

        if(is_object($obj)) {
            if($obj instanceof Arrayable) {
                return $obj->toArray();
            } else {
                throw new \Exception("Object received, expected array");
            }
        }
    }

    /**
     * Success no data
     * @return Response
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