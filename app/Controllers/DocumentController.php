<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 07/02/2019
 * Time: 14:26
 */

namespace App\Controllers;

class DocumentController extends BaseController
{

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

        $this->container->view->getEnvironment()->addGlobal('documents', $_SESSION['documents']);

    }

    public function requestDocument($request, $response)
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

        $this->container->view->getEnvironment()->addGlobal('documents', $_SESSION['documents']);

    }
}