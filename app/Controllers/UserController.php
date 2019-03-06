<?php

namespace App\Controllers;

use Respect\Validation\Validator as v;


class UserController extends BaseController
{

    public function requestUser($email, $path)
    {
        if ($path == null) {
            $path = "/user/" . $email;
        }

        $api_request = $this->tokenGetRequest($path);
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
                $this->container->view->getEnvironment()->addGlobal('user', $user);
                $_SESSION['user'] = $user;
            }
        } else if ($result_code == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result_code == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }

    }

    public function requestUserDetails($request, $response, $args)
    {
        $email = $args['email'];
        $path = "/user/" . $email;
        $this->requestUser($email, $path);
        if (isset($_SESSION['user'])) {
            $docs = new DocumentController($this->container);
            $docs->requestDocuments();
            return $this->view->render($response, 'user/details.twig');
        }
    }


    public function requestUsers()
    {
        $path = "/user/all";

        $api_request = $this->tokenGetRequest($path);

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

    public function notify($request, $response)
    {
        return $this->view->render($response, 'message/add.twig');
    }

    public function sendMessage($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'subject' => v::notEmpty(),
            'body' => v::notEmpty(),
        ]);


        $message = array(
            'subject' => $request->getParam('subject'),
            'body' => $request->getParam('body')
        );
        $api_request = $this->postTokenRequest($message);
        if (method_exists($api_request, 'getBody')) {
            $api_response = json_decode($api_request->getBody()->getContents());
            $result = $api_response->code;
        } else {
            $result = $api_request->getMessage()['status'];
        }

        if ($result == 200) {
            return $response->withRedirect($this->router->pathFor('home'));
        } else {
            $_SESSION['result_error'] = "Não autorizado, verifique as credenciais";
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
    }

}