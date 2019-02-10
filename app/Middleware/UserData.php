<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 07/02/2019
 * Time: 16:01
 */

namespace App\Middleware;


class UserData extends BaseMiddleware
{

    public function __invoke($request, $response, $next)
    {
        if (isset($_SESSION['user'])) {
            $this->container->view->getEnvironment()->addGlobal('user', $_SESSION['user']);
        }

        $response = $next($request, $response);
        return $response;
    }

}