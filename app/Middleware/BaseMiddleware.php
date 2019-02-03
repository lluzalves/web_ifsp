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
        if (isset($_SESSION['token'])) {
            $this->getToken();
        }

        $response = $next($request, $response);
        return $response;
    }

    public function getToken()
    {
        return $_SESSION['token'];
    }

}