<?php


/**
 * Class JobController Endpoints for /job/ api calls
 */
class JobController extends BaseController
{

    /**
     * Expects a POST request with details of a new job submission
     * @return \Phalcon\Http\Response
     */
    public function addAction()
    {

        $apiHelper = $this->di->get('apiHelper');
        


        if ($this->request->isPost()) {

            /**
             * @var $jobValidator \Api\Services\ValidateJobDefintion
             */
            $jobValidator = $this->di->get('jobValidator');

            $definition = $this->request->getPost('definition', 'trim');

            if (($messages = $jobValidator->validateDefiniton($definition))) {

                //Convert post data to our Json representation


                //Add job

                $response = $apiHelper->responseSuccess();

            } else {

                //Send back errors in job submission
                $response = $apiHelper->responseSuccess();

            }

        } else {
            $response = $apiHelper->responseWrongMethod();
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