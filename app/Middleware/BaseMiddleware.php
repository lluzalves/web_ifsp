<?php


namespace App\Middleware;


class BaseMiddleware
{

    protected $container;
    protected $token;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $next)
    {
        if (isset($_SESSION['role'])) {
            $this->container->view->getEnvironment()->addGlobal('role', $_SESSION['role']);
        }

        $response = $next($request, $response);
        return $response;
    }

    public function getToken()
    {
        if (isset($_SESSION['token']) && array_key_exists('token', $_SESSION)) {
            return $_SESSION['token'];
        } else
            return null;

    }

}