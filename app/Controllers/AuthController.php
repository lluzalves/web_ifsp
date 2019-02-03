<?php

namespace App\Controllers;

use App\Models\User;
use Slim\Views\Twig as View;
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
            'name' => v::notEmpty(),
            'password' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('auth.signup'));
        }

        $body = array(
            'name' => $request->getParam('name'),
            'email' => $request->getParam('email'),
            'password' => $request->getParam('password')
        );
        $path = "/register";
        $result = $this->requestPostWithParams($path, $body)->getCode();

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

        $auth = array(
            'name' => $request->getParam('name'),
            'password' => $request->getParam('password')
        );
        $path = "";
        $result = json_decode($this->requestPostWithParams($path, $auth));
        if ($result->code == 200) {
            return $response->withRedirect($this->router->pathFor('home'));
        } else {
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }
    }


}