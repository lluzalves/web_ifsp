<?php

namespace App\Controllers;


class UserController extends BaseController
{

    public function requestUser($email)
    {
        $path = "/user/" . $email;
        $api_request = $this->tokenRequest($path);
        if (method_exists($api_request, 'getCode')) {
            $result = $api_request;
            $result_code = $result->getCode();
        } else {
            $result = $api_request->getBody()->getContents();
            $result_code = json_decode($result)->code;
        }
        if ($result_code == 200) {
            $user = json_decode($result)->user;
            if ($user != null) {
                $_SESSION['user'] = true;
                $_SESSION['user'] = $user->id;
                $_SESSION['prontuario'] = $user->prontuario;
            } else {
                $_SESSION['user'] = false;
            }
        } else if ($result_code == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result_code == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }

    }

    public function requestUsers($request, $response)
    {
        $path = "/user/all";

        $api_request = $this->tokenRequest($path);

        if (method_exists($api_request, 'getCode')) {
            $result = $api_request;
            $result_code = $result->getCode();
        } else {
            $result = $api_request->getBody()->getContents();
            $result_code = json_decode($result)->code;
        }


        if ($result_code == 204) {
            if (property_exists(json_decode($result), 'users')) {
                $users = json_decode($result)->users;
                if (count($users) > 0) {
                    $_SESSION['anyUser'] = true;
                    $_SESSION['users'] = $users;
                    $this->container->view->getEnvironment()->addGlobal('users', $_SESSION['users']);
                }
            } else {
                $_SESSION['anyUser'] = false;
            }
        } else if ($result_code == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result_code == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }
    }
}