<?php

namespace App\Controllers;

use App\Models\User;
use function GuzzleHttp\Promise\exception_for;
use Respect\Validation\Validator as v;

class DocumentController extends BaseController
{

    public function requestDocuments($request, $response)
    {
        $path = "/documents";
        $api_request = $this->tokenRequest($path);
        if (method_exists($api_request, 'getCode')) {
            $result = $api_request;
            $result_code = $result->getCode();
        } else {
            $result = $api_request->getBody()->getContents();
            $result_code = json_decode($result)->code;
        }
        if ($result_code == 204) {
            if (property_exists(json_decode($result), 'documents')) {
                $documents = json_decode($result)->documents;
                if (count($documents) > 0) {
                    $_SESSION['anyDocuments'] = true;
                    $_SESSION['documents'] = $documents;
                    $this->container->view->getEnvironment()->addGlobal('documents', $_SESSION['documents']);
                }
            } else {
                $_SESSION['anyDocuments'] = false;
            }
        } else if ($result_code == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result_code == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }

    }

    public function requestDocument($request, $response)
    {
        $path = "/documents";
        $api_request = $this->tokenRequest($path);
        if (method_exists($api_request, 'getCode')) {
            $result = $api_request;
            $result_code = $result->getCode();
        } else {
            $result = $api_request->getBody()->getContents();
            $result_code = json_decode($result)->code;
        }
        if ($result_code == 204) {
            $documents = json_decode($result)->documents;
            if (count($documents) > 0) {
                $_SESSION['anyDocuments'] = true;
                $_SESSION['documents'] = $documents;
            } else {
                $_SESSION['anyDocuments'] = false;
            }
        } else if ($result_code == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result_code == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }

        $this->container->view->getEnvironment()->addGlobal('documents', $_SESSION['documents']);

    }

    public function addDocument($request, $response)
    {
        $path = "/documents";

        $files = $request->getUploadedFiles();
        $file = $files['file'];
        $filename = json_encode($files);
        $isFileAttacched = v::notBlank()->validate(json_decode($filename)->file);

        $user = new UserController($this->container);
        $user->requestUser($_SESSION['email']);

        $type = ($_POST['type']);

        $validation = $this->validator->validate($request, [
            'description' => v::notBlank(),
        ]);

        if (!$isFileAttacched) {
            $_SESSION['file'] = null;
            $this->container->view->getEnvironment()->addGlobal('file', $_SESSION['file']);
        }

        if ($validation->failed() || !$isFileAttacched) {
            return $response->withRedirect($this->router->pathFor('document.add'));
        }

        $description = [
            'Content-type' => 'multipart/form-data',
            'name' => 'description',
            'contents' => $request->getParam('description'),
        ];

        $userId = [
            'Content-type' => 'multipart/form-data',
            'name' => 'user_id',
            'contents' => $_SESSION['user'],
        ];
        $type = [
            'Content-type' => 'multipart/form-data',
            'name' => 'type',
            'contents' => $type,
        ];

        $path = "/documents";
        $api_request = $this->multiPartTokenRequest($path, $description, $userId, $type, $file);
        if (method_exists($api_request, 'getCode')) {
            $result = $api_request->getCode();
        } else {
            $result = 200;
        }

        if ($result == 200) {
            return $response->withRedirect($this->router->pathFor('home'));
        } else if ($result == 409) {
            $_SESSION['result_error'] = "Email inválido, já cadastrado";
        } else if ($result == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }
        return $response->withRedirect($this->router->pathFor('document.add'));
    }

    public function showAddForm($request, $response)
    {
        return $this->view->render($response, 'document/add.twig');
    }
}