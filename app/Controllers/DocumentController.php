<?php

namespace App\Controllers;

use App\Models\User;
use Respect\Validation\Validator as v;

class DocumentController extends BaseController
{

    public function requestDocuments()
    {
        $path = "/documents";

        if ($_SESSION['role'] == "admin") {
            $body = array('user_id' => $_SESSION['user']->id);
            $api_request = $this->tokenGetRequestWithQuery($path, $body);
        } else {
            $api_request = $this->tokenGetRequest($path);
        }
        if (method_exists($api_request, 'getCode')) {
            $result = $api_request;
            $result_code = $result->getCode();
        } else {
            $result = $api_request->getBody()->getContents();
            $result_code = json_decode($result)->code;
        }
        if ($result_code == 200) {
            if (property_exists(json_decode($result), 'documents')) {
                $documents = json_decode($result)->documents;
                if (count($documents) > 0) {
                    $_SESSION['documents'] = $documents;
                    $this->container->view->getEnvironment()->addGlobal('documents', $_SESSION['documents']);
                }
            }
        } else if ($result_code == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result_code == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }
    }

    public function requestDocument($request, $response, $args)
    {
        $path = "/documents/" . $args['document_id'];
        $api_request = $this->tokenGetRequest($path);

        if (method_exists($api_request, 'getCode')) {
            $result = $api_request;
            $result_code = $result->getCode();
        } else {
            $result = $api_request->getBody()->getContents();
            $result_code = json_decode($result)->code;
        }

        if ($result_code == 200) {
            $document = json_decode($result)->documents[0];
            if ($document != null) {
                $_SESSION['anyDocuments'] = true;
                $_SESSION['document'] = $document;
            } else {
                $_SESSION['anyDocuments'] = false;
            }
        } else if ($result_code == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result_code == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }

        $this->container->view->getEnvironment()->addGlobal('document', $_SESSION['document']);
        return $this->view->render($response, 'document/details.twig');
    }


    public function requestDocumentAttachment($request, $response, $args)
    {
        $path = "/documents/" . $args['document_id'] . '/attachment';
        $api_request = $this->tokenStreamRequest($path);
        return $api_request->withHeader('Content-Type', 'application/download');
    }

    public function addDocument($request, $response)
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
            unset($_SESSION['document']);
        }

        if (isset($_SESSION['document']) && isset($_SESSION['user_id'])) {
            $body = [
                [
                    'Content-type' => 'multipart/form-data',
                    'name' => 'id',
                    'contents' => $_SESSION['document']->id,
                ],
                [
                    'Content-type' => 'multipart/form-data',
                    'name' => 'user_id',
                    'contents' => $_SESSION['user_id'],
                ],
                [
                    'Content-type' => 'multipart/form-data',
                    'name' => 'description',
                    'contents' => $request->getParam('description'),
                ],

                [
                    'Content-type' => 'multipart/form-data',
                    'name' => 'type',
                    'contents' => $type,
                ]
            ];
            $path = "/documents/upsert";
        } elseif (!isset($_SESSION['document']) && isset($_SESSION['user_id'])) {
            $body = [
                [
                    'Content-type' => 'multipart/form-data',
                    'name' => 'user_id',
                    'contents' => $_SESSION['user_id'],
                ],
                [
                    'Content-type' => 'multipart/form-data',
                    'name' => 'description',
                    'contents' => $request->getParam('description'),
                ],

                [
                    'Content-type' => 'multipart/form-data',
                    'name' => 'type',
                    'contents' => $type,
                ]
            ];
            $path = "/documents/upsert";
        } else {
            $body = [
                [
                    'Content-type' => 'multipart/form-data',
                    'name' => 'description',
                    'contents' => $request->getParam('description'),
                ],

                [
                    'Content-type' => 'multipart/form-data',
                    'name' => 'type',
                    'contents' => $type,
                ]
            ];
            $path = "/documents";
        }
        $api_request = $this->multiPartTokenRequest($path, $body, $user, $file);
        if (method_exists($api_request, 'getCode')) {
            $result = $api_request->getCode();
        } else {
            $result = 200;
        }

        if ($result == 200) {
            unset($_SESSION['user_id']);
            unset($_SESSION['document']);
            return $response->withRedirect($this->router->pathFor('home'));
        } else if ($result == 500) {
            $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
        } else if ($result == 401) {
            $_SESSION['result_error'] = "Não autorizado";
        }
        return $response->withRedirect($this->router->pathFor('document.add'));
    }

    public function delete($request, $response, $args)
    {
        $path = "/documents/" . $args['document_id'];
        $api_request = $this->tokenDeleteRequest($path);
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
    }

    public function updateSessionDocument($request, $response, $args)
    {
        foreach ($_SESSION['documents'] as $document) {
            if ($document->id = $args['document_id']) {
                $_SESSION['document'] = $document;
                $_SESSION['user_id'] = $document->user_id;
                $this->container->view->getEnvironment()->addGlobal('document', $document);
                break;
            }
        }
        $this->showAddForm($request, $response);
    }

    public function updateSessionUserId($request, $response, $args)
    {
        $_SESSION['user_id'] = $args['user_id'];
        $this->showAddForm($request, $response);
    }

    public function showAddForm($request, $response)
    {
        return $this->view->render($response, 'document/add.twig');
    }

    public function validateDocument($resquest, $response, $args)
    {
        $path = "/documents/validate/" . $args['document_id'];

        if ($_SESSION['role'] == "admin") {


            $body = [
                [
                    'Content-type' => 'multipart/form-data',
                    'name' => 'document_id',
                    'contents' => $args['document_id'],
                ],

                [
                    'Content-type' => 'multipart/form-data',
                    'name' => 'is_validated',
                    'contents' => true,
                ]
            ];


            $api_request = $this->postTokenRequest($path, $body);

            if (method_exists($api_request, 'getBody')) {
                $api_response = json_decode($api_request->getBody()->getContents());
                $result = $api_response->code;
            } else {;
                $result = $api_request->getMessage()['status'];
            }

            if ($result == 200) {
                $user_controller = new UserController($this->container);
                return $user_controller->requestUserDetails($response,$response,$args);
            } else if ($result == 500) {
                $_SESSION['result_error'] = "Requisição inválida, tente novamente mais tarde";
            } else if ($result == 401) {
                $_SESSION['result_error'] = "Não autorizado";
            }
        }
    }

}