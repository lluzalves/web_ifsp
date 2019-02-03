<?php

namespace App\Controllers;
class HomeController extends BaseController
{

    public function index($request, $response)
    {
        $this->view->getEnvironment()->addGlobal('email',$this->getEmail());
        return $this->view->render($response, 'home.twig');
    }
}