<?php

/*** Project: autoq}.
 * User: bstratton
 * Date: 16/04/2016
 * Time: 14:06
 */

use Phalcon\Mvc\Controller;


class Error404Controller extends BaseController
{
    public function indexAction()
    {

        $response = new \Phalcon\Http\Response();

        $response->setStatusCode(404);

        $response->setContent("Page not found");

        return $response;
    }
}