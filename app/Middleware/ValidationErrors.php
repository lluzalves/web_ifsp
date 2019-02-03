<?php


namespace App\Middleware;


class ValidationErrors extends BaseMiddleware
{

    public function __invoke($request, $response, $next)
    {
        if(isset($_SESSION['errors'])){
            $this->container->view->getEnvironment()->addGlobal('errors', $_SESSION['errors']);
            unset($_SESSION['errors']);
        }

        if(isset($_SESSION['result_error'])){
            $this->container->view->getEnvironment()->addGlobal('result_error', $_SESSION['result_error']);
            unset($_SESSION['result_error']);
        }

        $response = $next($request, $response);
        return $response;
    }

}