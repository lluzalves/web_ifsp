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

        $requestedUri = $request->getServerParams()['REQUEST_URI'];
        if (strpos($requestedUri, '/web_ifsp/public/document') !== false ||
            strpos($requestedUri, '/web_ifsp/public/users') !== false) {
            if ($this->getToken() == null) {
                return $response->withRedirect($this->container->router->pathFor('auth.signin'));
            }
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

    public function checkToken()
    {
        if (isset($_SESSION['token']) && array_key_exists('token', $_SESSION)) {
            return $_SESSION['token'];
        } else
            return null;
    }

}