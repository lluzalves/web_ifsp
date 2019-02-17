<?php

namespace App\Controllers;

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

}