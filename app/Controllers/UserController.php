<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 09/02/2019
 * Time: 19:34
 */

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
            } else {
                $_SESSION['user'] = false;
            }
        } else if ($result_code == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result_code == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }

    }

}