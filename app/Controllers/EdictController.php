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
        $roles = ($_POST['roles']);
        $validation = $this->validator->validate($request, [
            'description' => v::notEmpty(),
            'title' => v::notEmpty(),
            'starts_at' => v::notEmpty(),
            'end_at' => v::notEmpty(),
            'roles' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('edict.add'));
        }

        $roles = [
            'Content-type' => 'multipart/form-data',
            'name' => 'roles',
            'contents' => $roles,
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
        $body = array($roles, $title, $description, $starts_at, $end_at, $created_by,);

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
}