<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 07/02/2019
 * Time: 16:01
 */

namespace App\Middleware;


class DocumentData extends BaseMiddleware
{

    public function __invoke($request, $response, $next)
    {
        if (isset($_SESSION['anyDocuments'])) {
            $this->container->view->getEnvironment()->addGlobal('anyDocuments', $_SESSION['anyDocuments']);
            $_SESSION['anyDocuments'] = $request->getParams();
        }

        if (isset($_SESSION['documents'])) {
            $this->container->view->getEnvironment()->addGlobal('documents', $_SESSION['documents']);
            $_SESSION['documents'] = $request->getParams();
        }


        $response = $next($request, $response);
        return $response;
    }

}