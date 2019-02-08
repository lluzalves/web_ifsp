<?php

namespace App\Controllers;
class HomeController extends BaseController
{

    public function index($request, $response)
    {
        $this->view->getEnvironment()->addGlobal('email', $this->getEmail());
        $doc = new DocumentController($this->container);
        $doc->requestDocuments($request, $response);
        return $this->view->render($response, 'home.twig');
    }

}