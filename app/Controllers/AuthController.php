<?php

namespace App\Controllers;

use Respect\Validation\Validator as v;

class AuthController extends BaseController
{
    public function getSignUp($request, $response)
    {
        return $this->view->render($response, 'auth/signup.twig');
    }

    public function getSignIn($request, $response)
    {
        return $this->view->render($response, 'auth/signin.twig');
    }

    public function postSignUp($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'email' => v::email(),
            'prontuario' => v::notBlank(),
            'name' => v::notEmpty(),
            'password' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('auth.signup'));
        }
        $name = [
            'Content-type' => 'multipart/form-data',
            'name' => 'name',
            'contents' => $request->getParam('name')
        ];

        $email = [
            'Content-type' => 'multipart/form-data',
            'name' => 'email',
            'contents' => $request->getParam('email')
        ];

        $password = [
            'Content-type' => 'multipart/form-data',
            'name' => 'password',
            'contents' => $request->getParam('password')
        ];

        $prontuario = [
            'Content-type' => 'multipart/form-data',
            'name' => 'prontuario',
            'contents' => $request->getParam('prontuario')
        ];

        $role = [
            'Content-type' => 'multipart/form-data',
            'name' => 'role',
            'contents' => 'aluno'
        ];

        $profile_icon = [
            'Content-type' => 'multipart/form-data',
            'name' => 'profile_icon',
            'contents' => 'aluno_basic'
        ];

        $body = array($name,$password,$email,$profile_icon,$prontuario,$role);

        $path = "/register";
        $api_request = $this->makePostRequestWithParams($path, $body);
        if (method_exists($api_request, 'getCode')) {
            $result = $api_request->getCode();
        } else {
            $result = 200;
        }

        if ($result == 200) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        } else if ($result == 409) {
            $_SESSION['result_error'] = "Email inválido, já cadastrado";
        } else if ($result == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }
        return $response->withRedirect($this->router->pathFor('auth.signup'));

    }

    public function postSignIn($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'email' => v::email(),
            'password' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        $auth = array(
            'email' => $request->getParam('email'),
            'password' => $request->getParam('password')
        );
        $api_request = $this->basicAuthRequest($auth);
        if (method_exists($api_request, 'getBody')) {
            $api_response = json_decode($api_request->getBody()->getContents());
            $result = $api_response->code;
        } else {
            $result = $api_request->getMessage()['status'];
        }

        if ($result == 200) {
            $_SESSION['email'] = $request->getParam('email');
            $_SESSION['token'] = $api_response->token;
            $_SESSION['role'] = $api_response->role;
            return $response->withRedirect($this->router->pathFor('home'));
        } else {
            $_SESSION['result_error'] = "Não autorizado, verifique as credenciais";
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
    }

    public function recoverCredentials($request, $response)
    {
        return $this->view->render($response, 'recover.twig');
    }

    public function logout($request, $response)
    {
        session_destroy();
        return $response->withRedirect($this->router->pathFor('home'));
    }

    public function terms($request, $response)
    {
        return $this->view->render($response, 'auth/terms.twig');
    }

    public function restartCredentials($request, $response)
    {
        $path = "/recover";
        $body = array(
            'email' => $request->getParam('email')
        );
        $api_request = $this->requestWithBody($path, $body);
        if (method_exists($api_request, 'getCode')) {
            $result = $api_request->getCode();
        } else {
            $result = 200;
        }
        if ($result == 200) {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        } else if ($result == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result == 401) {
            $_SESSION['result_error'] = "Não autorizado, tente novamente";
        }
        return $response->withRedirect($this->router->pathFor('auth.signup'));

    }
}