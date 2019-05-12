<?php


namespace App\Controllers;


use Respect\Validation\Validator as v;

class EdictController extends BaseController
{
    public function showAddForm($request, $response)
    {
        return $this->view->render($response, 'edicts/add.twig');
    }

    public function addEdict($request, $response)
    {
        $requestUser = new UserController($this->container);
        $requestUser->requestUserByEmail($_SESSION['email'], null);
        $user = $_SESSION['user'];
        $type = ($_POST['type']);
        $validation = $this->validator->validate($request, [
            'description' => v::notEmpty(),
            'title' => v::notEmpty(),
            'starts_at' => v::notEmpty(),
            'end_at' => v::notEmpty(),
            'type' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('edict.add'));
        }

        $type = [
            'Content-type' => 'multipart/form-data',
            'name' => 'type',
            'contents' => $type,
        ];

        $created_by = [
            'Content-type' => 'multipart/form-data',
            'name' => 'created_by',
            'contents' => $user->id,
        ];

        $description = [
            'Content-type' => 'multipart/form-data',
            'name' => 'description',
            'contents' => $request->getParam('description'),
        ];
        $title = [
            'Content-type' => 'multipart/form-data',
            'name' => 'title',
            'contents' => $request->getParam('title'),
        ];
        $starts_at = [
            'Content-type' => 'multipart/form-data',
            'name' => 'starts_at',
            'contents' => $request->getParam('starts_at'),
        ];

        $end_at = [
            'Content-type' => 'multipart/form-data',
            'name' => 'end_at',
            'contents' => $request->getParam('end_at'),
        ];

        $path = "/edict/add";
        $body = array($type, $title, $description, $starts_at, $end_at, $created_by,);

        $api_request = $this->makePostRequestWithToken($path, $body);
        if (method_exists($api_request, 'getCode')) {
            $result = $api_request->getCode();
        } else {
            $result = 200;
        }

        if ($result == 200) {
            return $response->withRedirect($this->router->pathFor('home'));
        } else if ($result == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }
        return $response->withRedirect($this->router->pathFor('edict.add'));
    }


    public function requestEdicts()
    {

        if($_SESSION['role'] === 'aluno') {
            $path = "/edict/user/all";
        }else{
            $path = "/edict/all";
        }

        $api_request = $this->tokenGetRequest($path);

        if (method_exists($api_request, 'getCode')) {
            $result = $api_request;
            $result_code = $result->getCode();
        } else {
            $result = $api_request->getBody()->getContents();
            $result_code = json_decode($result)->code;
        }

        if ($result_code == 200) {
            if (property_exists(json_decode($result), 'edicts')) {
                $edicts = json_decode($result)->edicts;
                if (count($edicts) > 0) {
                    $_SESSION['anyEdict'] = true;
                    $_SESSION['edicts'] = $edicts;
                    $this->container->view->getEnvironment()->addGlobal('edicts', $_SESSION['edicts']);
                }
            } else {
                $_SESSION['anyEdict'] = false;
            }
        } else if ($result_code == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result_code == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }
    }


    public function requestEdictDetails($request, $response, $args)
    {
        $path = "/edict/details/" . $args['edict_id'];
        $api_request = $this->tokenGetRequest($path);

        if (method_exists($api_request, 'getCode')) {
            $result = $api_request;
            $result_code = $result->getCode();
        } else {
            $result = $api_request->getBody()->getContents();
            $result_code = json_decode($result)->code;
        }

        if ($result_code == 200) {
            $edict = json_decode($result)->edict[0];
            if ($edict != null) {
                $_SESSION['edict'] = $edict;
            }
        } else if ($result_code == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result_code == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }

        $this->container->view->getEnvironment()->addGlobal('edict', $_SESSION['edict']);
        return $this->view->render($response, 'edicts/details.twig');
    }
}