<?php

namespace App\Controllers;

class UserController extends BaseController
{

    public function requestUserByEmail($email, $path)
    {
        if ($path == null) {
            $this->requestUser("/user/" . $email);
        } else {
            $this->requestUser($path);
        }

    }

    public function requestUser($path)
    {
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
            unset($_SESSION['user']);
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result_code == 401) {
            unset($_SESSION['user']);
            $_SESSION['result_error'] = "Não autorizado";
        }
    }

    public function requestUserDetails($request, $response, $args)
    {
        if (isset($args['email'])) {
            $email = $args['email'];
            $_SESSION['current_detailed_user_email'] = $email;
        } else {
            $email = $_SESSION['user']->email;
        }
        $path = "/user/" . $email;
        $this->requestUserByEmail($email, $path);
        if (isset($_SESSION['user'])) {
            $docs = new DocumentController($this->container);
            $docs->requestDocuments();
            return $this->view->render($response, 'user/details.twig');
        }
    }


    public function requestUsers($request, $response, $args)
    {
        $path = "/user/all/edict/" . $args['edict_id'];

        $api_request = $this->tokenGetRequest($path);

        if (method_exists($api_request, 'getCode')) {
            $result = $api_request;
            $result_code = $result->getCode();
        } else {
            $result = $api_request->getBody()->getContents();
            $result_code = json_decode($result)->code;
        }

         if ($result_code == 200) {
            if (property_exists(json_decode($result), 'users')) {
                $users = json_decode($result)->users;
                if (count($users) > 0) {
                    $_SESSION['anyUser'] = true;
                    $_SESSION['users'] = $users;
                    $this->container->view->getEnvironment()->addGlobal('users', $_SESSION['users']);
                    return $this->view->render($response, 'user/all.twig');
                }
            } else {
                $_SESSION['anyUser'] = false;
                return $this->view->render($response, 'user/all.twig');
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

    public function filter($request, $response)
    {
        $this->requestUser("/user/filter/" . $request->getParam('prontuario'));
        return $this->view->render($response, 'user/filter.twig');
    }

}