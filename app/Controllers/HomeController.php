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
            $result = $api_request;
            $result_code = $result->getCode();
        } else {
            $result = $api_request->getBody()->getContents();
            $result_code = json_decode($result)->code;
        }
        if ($result_code == 204) {
            $documents = json_decode($result)->documents;
            if (count($documents) > 0) {
                $_SESSION['anyDocuments'] = true;
                $_SESSION['documents'] = $documents;
            } else {
                $_SESSION['anyDocuments'] = false;
            }
        } else if ($result_code == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result_code == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }
        return $response->withRedirect($this->router->pathFor('auth.signup'));

    }
}