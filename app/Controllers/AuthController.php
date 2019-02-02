<?php

namespace App\Controllers;

use App\Models\User;
use Slim\Views\Twig as View;

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

        $body = array(
            'name' => $request->getParam('name'),
            'email' => $request->getParam('email'),
            'password' => $request->getParam('password')
        );
        $path = "/register";
        $result = json_decode($this->requestPostWithParams($path, $body));
        if($result->code == 200){
            return $response->withRedirect($this->router->pathFor('login'));
        }else{
            return $response->withRedirect($this->router->pathFor('auth.signup'));
        }
    }


}