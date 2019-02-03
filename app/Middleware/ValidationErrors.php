<?php


namespace App\Middleware;


class ValidationErrors extends BaseMiddleware
{

    public function __invoke($request, $response, $next)
    {
        $this->container->view->getEnvironment()->addGlobal('errors', $_SESSION['errors']);
        unset($_SESSION['errors']);

        $response = $next($request, $response);
        return $response;
    }

}