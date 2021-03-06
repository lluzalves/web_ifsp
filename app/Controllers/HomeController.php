<?php

namespace App\Controllers;

use App\Middleware\BaseMiddleware;

class HomeController extends BaseController
{
    public function index($request, $response)
    {
        $this->view->getEnvironment()->addGlobal('email', $this->getEmail());
        $doc = new DocumentController($this->container);
        $edict = new EdictController($this->container);
        if (BaseMiddleware::getToken() != null) {
            $notification = new NotificationController($this->container);
            $notification->requestNotifications();
            if ($_SESSION['role'] === 'aluno') {
                $doc->requestDocuments();
            } else if ($_SESSION['role'] === 'admin') {
                $edict->requestEdicts();
            }
            return $this->view->render($response, 'home.twig');
        } else {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
    }

}