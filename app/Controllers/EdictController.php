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

        $files = $request->getUploadedFiles();
        $file = $files['file'];
        $filename = json_encode($files);
        $isFileAttached = v::notBlank()->validate(json_decode($filename)->file);

        $requestUser = new UserController($this->container);
        $requestUser->requestUserByEmail($_SESSION['email'], null);
        $user = $_SESSION['user'];
        $type = ($_POST['type']);

        $validation = $this->validator->validate($request, [
            'description' => v::notBlank(),
        ]);

        if (!$isFileAttached) {
            $_SESSION['file'] = null;
            $this->container->view->getEnvironment()->addGlobal('file', $_SESSION['file']);
        }
        if ($validation->failed() || !$isFileAttached) {
            $_SESSION['result_error'] = $validation;
            unset($_SESSION['user_id']);
            unset($_SESSION['edict']);
        }

        $body = [
            [
                'Content-type' => 'multipart/form-data',
                'name' => 'id',
                'contents' => $_SESSION['edict']->id,
            ],
            [
                'Content-type' => 'multipart/form-data',
                'name' => 'type',
                'contents' => $type,
            ],
            [
                'Content-type' => 'multipart/form-data',
                'name' => 'created_by',
                'contents' => $_SESSION['user_id'],
            ],
            [
                'Content-type' => 'multipart/form-data',
                'name' => 'description',
                'contents' => $request->getParam('description'),
            ],
            [
                'Content-type' => 'multipart/form-data',
                'name' => 'title',
                'contents' => $request->getParam('title'),
            ],
            [
                'Content-type' => 'multipart/form-data',
                'name' => 'elegilable_roles',
                'contents' => $request->getParam('elegilable_roles'),
            ],
            [
                'Content-type' => 'multipart/form-data',
                'name' => 'starts_at',
                'contents' => $request->getParam('starts_at'),
            ],
            [
                'Content-type' => 'multipart/form-data',
                'name' => 'end_at',
                'contents' => $request->getParam('end_at'),
            ],

            [
                'Content-type' => 'multipart/form-data',
                'name' => 'type',
                'contents' => $type,
            ]
        ];
        $path = "/edict/add";
        $api_request = $this->multiPartTokenRequest($path, $body, $user, $file);
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