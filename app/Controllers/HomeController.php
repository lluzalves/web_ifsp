<?php

namespace App\Controllers;
class HomeController extends BaseController
{

    public function index($request, $response)
    {
        $this->view->getEnvironment()->addGlobal('email', $this->getEmail());
        $this->requestDocuments($request, $response);
        return $this->view->render($response, 'home.twig');
    }

    public function requestDocuments($request, $response)
    {
        $path = "/documents";
        $api_request = $this->tokenRequest($path);
        if (method_exists($api_request, 'getCode')) {
            $result = $api_request->getCode();
        } else {
            $result = 200;
        }

        if ($result == 204) {
           $_SESSION['anyDocument'] = false;
        } else if ($result == 200) {
            $_SESSION['anyDocument'] = true;
        } else if ($result == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }
        return $response->withRedirect($this->router->pathFor('auth.signup'));

    }
}