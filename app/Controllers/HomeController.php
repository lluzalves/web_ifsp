<?php

namespace App\Controllers;

use App\Middleware\BaseMiddleware;

class HomeController extends BaseController
{

    public function index($request, $response)
    {
        $this->view->getEnvironment()->addGlobal('email', $this->getEmail());
        $doc = new DocumentController($this->container);
        $user = new UserController($this->container);
        if (BaseMiddleware::getToken() != null) {
            if ($_SESSION['role'] === 'aluno') {
                $doc->requestDocuments($request, $response);
            } else if ($_SESSION['role'] === 'admin') {
                $user->requestUsers($request, $response);
            }
            sleep(2);
            return $this->view->render($response, 'home.twig');
        } else {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
    }

}