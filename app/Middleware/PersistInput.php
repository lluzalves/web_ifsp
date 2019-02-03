<?php

namespace App\Middleware;


class PersistInput  extends BaseMiddleware
{

    public function __invoke($request, $response, $next)
    {
        $this->container->view->getEnvironment()->addGlobal('oldData', $_SESSION['oldData']);
        $_SESSION['oldData'] = $request->getParams();

        $response = $next($request, $response);
        return $response;
    }

}