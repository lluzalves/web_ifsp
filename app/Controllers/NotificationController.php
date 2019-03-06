<?php

namespace App\Controllers;

use Respect\Validation\Validator as v;


class NotificationController extends BaseController
{

    public function requestNotifications()
    {

        $path = "/notifications";

        $api_request = $this->tokenGetRequest($path);

        if (method_exists($api_request, 'getCode')) {
            $result = $api_request;
            $result_code = $result->getCode();
        } else {
            $result = $api_request->getBody()->getContents();
            $result_code = json_decode($result)->code;
        }

        if ($result_code == 204) {
            if (property_exists(json_decode($result), 'notifications')) {
                $notifications = json_decode($result)->notifications;
                if (count($notifications) > 0) {
                    $_SESSION['notifications'] = $notifications;
                    $this->container->view->getEnvironment()->addGlobal('notifications', $_SESSION['notifications']);
                }
            }
        } else if ($result_code == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result_code == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }
    }

    public function sendNotification($request, $response)
    {
        $path = "/notifications/" . $_SESSION['user']->id;


        $validation = $this->validator->validate($request, [
            'subject' => v::notEmpty(),
            'body' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            $_SESSION['result_error'] = $validation;
        }


        $message = [
            [
                'Content-type' => 'multipart/form-data',
                'name' => 'subject',
                'contents' => $request->getParam('subject'),
            ],

            [
                'Content-type' => 'multipart/form-data',
                'name' => 'body',
                'contents' => $request->getParam('body'),
            ],

            [
                'Content-type' => 'multipart/form-data',
                'name' => 'receiver_id',
                'contents' => $_SESSION['user']->id,
            ]
        ];

        $api_request = $this->postTokenRequest($path, $message);
        if (method_exists($api_request, 'getBody')) {
            $api_response = json_decode($api_request->getBody()->getContents());
            $result = $api_response->code;
        } else {
            $result = $api_request->getMessage()['status'];
        }

        if ($result == 204) {
            return $response->withRedirect($this->router->pathFor('home'));
        } else if ($result == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }
    }

}