<?php

namespace Autoq\Controllers;


use Autoq\Services\ApiHelper;
use Autoq\Services\DbConnectionMgr;
use Phalcon\Dispatcher;

/**
 * Class SecureController
 */
class SecureController extends BaseController
{

    /**
     * Event executed before controller is run
     * @param Dispatcher $dispatcher
     * @return bool
     */
    protected function beforeExecuteRoute(Dispatcher $dispatcher)
    {
        if(getenv('AUTOQ_AUTH') === "0") {
            return true;
        }

        $apiKey = $this->request->get('apikey');

        $di = $dispatcher->getDI();

        /**
         * @var $dbConnectionMgr DbConnectionMgr
         */
        $dbConnectionMgr = $di->get('dBConnectionMgr');

        /**
         * @var $apiHelper ApiHelper
         */
        $apiHelper = $di->get('apiHelper');

        if ($apiKey != "") {

            try {
                if (($connection = $dbConnectionMgr->getConnection('mysql')) !== null) {

                    if (($row = $connection->fetchOne("SELECT * FROM api_access_keys WHERE api_key='$apiKey' AND active = 'YES'"))) {

                        return true;  //we found a match for the api key, allow access

                    } else {
                        $dispatcher->forward(['controller' => 'Error', 'action' => 'response', 'params' => [$apiHelper->responseError("Auth: Invalid api key provided")]]);
                        return false;
                    }

                } else {
                    $dispatcher->forward(['controller' => 'Error', 'action' => 'response', 'params' => [$apiHelper->responseError("Error: Unable to connect to Autoq database")]]);
                    return false;
                }

            } catch (\Exception $e) {
                $dispatcher->forward(['controller' => 'Error', 'action' => 'response', 'params' => [$apiHelper->responseError("Error: " . $e->getMessage())]]);
                return false;
            }
        } else {
            $dispatcher->forward(['controller' => 'Error', 'action' => 'response', 'params' => [$apiHelper->responseError("Auth: No api key provided")]]);
            return false;
        }
    }
}