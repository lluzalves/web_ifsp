<?php

namespace App\Controllers;
use App\Middleware\BaseMiddleware;

class HomeController extends BaseController
{

    public function index($request, $response)
    {
        $this->view->getEnvironment()->addGlobal('email', $this->getEmail());
        $doc = new DocumentController($this->container);
        if(BaseMiddleware::getToken()!=null) {
            $doc->requestDocuments($request, $response);
            sleep(3);
            return $this->view->render($response, 'home.twig');
        }else{
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
    }

}