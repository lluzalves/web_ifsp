<?php

namespace App\Controllers;

use App\Middleware\BaseMiddleware;
use Interop\Container\ContainerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\BadResponseException;

abstract class BaseController
{

    protected $container;
    protected $view;
    protected $client;
    protected $router;
    protected $validator;
    protected $api_response;
    protected $api_address = 'http://192.168.0.22/slim_app/public';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->view = $container->view;
        $this->router = $container->router;
        $this->validator = $container->validator;
        $this->client = new Client();
    }

    public function request($path, $method)
    {
        $api_request = $this->client->request(
            $method,
            $this->api_address . $path
        );
        return $api_request;
    }

    public function requestWithBody($path, $body)
    {
        try {
            $this->api_response = $this->client->post(
                $this->api_address . $path, [
                'multipart' => [
                    [
                        'Content-type' => 'multipart/form-data',
                        'name' => 'email',
                        'contents' => $body['email'],
                    ]
                ]
            ]);
        } catch (ServerException $server_exception) {
            $this->api_response = $server_exception;
        } catch (ClientException $client_exception) {
            $this->api_response = $client_exception;
        } catch (BadResponseException $response_exception) {
            $this->api_response = $response_exception;
        }
        return $this->api_response;
    }

    public function basicAuthRequest($body)
    {

        $credentials = base64_encode($body['email'] . ':' . $body['password']);
        try {
            $this->api_response = $this->client->post(
                $this->api_address, [
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials
                ]
            ]);
        } catch (ServerException $server_exception) {
            $this->api_response = $server_exception;
        } catch (ClientException $client_exception) {
            $this->api_response = $client_exception;
        } catch (BadResponseException $response_exception) {
            $this->api_response = $response_exception;
        }

        return $this->api_response;
    }

    public function tokenGetRequest($path)
    {
        $credentials = BaseMiddleware::getToken();
        try {
            $this->api_response = $this->client->get(
                $this->api_address . $path, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $credentials
                ]
            ]);
        } catch (ServerException $server_exception) {
            $this->api_response = $server_exception;
        } catch (ClientException $client_exception) {
            $this->api_response = $client_exception;
        } catch (BadResponseException $response_exception) {
            $this->api_response = $response_exception;
        }

        return $this->api_response;
    }

    public function tokenGetRequestWithQuery($path, $body)
    {
        $credentials = BaseMiddleware::getToken();
        try {
            $this->api_response = $this->client->get(
                $this->api_address . $path, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $credentials
                ],
                'query' => [$body

                ]
            ]);
        } catch (ServerException $server_exception) {
            $this->api_response = $server_exception;
        } catch (ClientException $client_exception) {
            $this->api_response = $client_exception;
        } catch (BadResponseException $response_exception) {
            $this->api_response = $response_exception;
        }

        return $this->api_response;
    }

    public function tokenStreamRequest($path)
    {
        $credentials = BaseMiddleware::getToken();
        try {
            $this->api_response = $this->client->get(
                $this->api_address . $path, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $credentials
                ],
            ]);
        } catch (ServerException $server_exception) {
            $this->api_response = $server_exception;
        } catch (ClientException $client_exception) {
            $this->api_response = $client_exception;
        } catch (BadResponseException $response_exception) {
            $this->api_response = $response_exception;
        }

        return $this->api_response;
    }

    public function tokenDeleteRequest($path)
    {
        $credentials = BaseMiddleware::getToken();
        try {
            $this->api_response = $this->client->delete(
                $this->api_address . $path, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $credentials
                ],
            ]);
        } catch (ServerException $server_exception) {
            $this->api_response = $server_exception;
        } catch (ClientException $client_exception) {
            $this->api_response = $client_exception;
        } catch (BadResponseException $response_exception) {
            $this->api_response = $response_exception;
        }

        return $this->api_response;
    }

    public function postTokenRequest($path, $data)
    {

        $credentials = BaseMiddleware::getToken();
        try {
            $this->api_response = $this->client->post(
                $this->api_address . $path, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $credentials
                ],
                'form-data' => [$data

                ]
            ]);
        } catch (ServerException $server_exception) {
            $this->api_response = $server_exception;
        } catch (ClientException $client_exception) {
            $this->api_response = $client_exception;
        } catch (BadResponseException $response_exception) {
            $this->api_response = $response_exception;
        }

        return $this->api_response;
    }


    public function multiPartTokenRequest($path, $description, $user, $type, $file)
    {

        if ($file->getError() === UPLOAD_ERR_OK) {
            $extension = pathinfo($file->getClientFileName(), PATHINFO_EXTENSION);
            $baseName = bin2hex(random_bytes(8));
            $filename = sprintf("%s.%0.8s", $baseName, $extension);
            $directory = 'upload' . DIRECTORY_SEPARATOR . $user->prontuario;
            mkdir($directory, 0700, true);
            $uploadedFilePath = $directory . DIRECTORY_SEPARATOR . $filename;
            $file->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        }

        $credentials = BaseMiddleware::getToken();
        try {
            $this->api_response = $this->client->post(
                $this->api_address . $path, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $credentials
                ],
                'multipart' => [
                    [
                        'Content-type' => 'multipart/form-data',
                        'name' => 'file',
                        'contents' => fopen($uploadedFilePath, 'r'),
                    ],
                    $description,
                    [
                        'Content-type' => 'multipart/form-data',
                        'name' => 'user_id',
                        'contents' => $user->id,
                    ],
                    $type
                ]
            ]);
        } catch (ServerException $server_exception) {
            $this->api_response = $server_exception;
        } catch (ClientException $client_exception) {
            $this->api_response = $client_exception;
        } catch (BadResponseException $response_exception) {
            $this->api_response = $response_exception;
        }

        fclose($uploadedFilePath);
        unlink($uploadedFilePath);
        return $this->api_response;
    }


    public function requestSignInPostWithParams($path, $body)
    {

        try {
            $this->api_response = $this->client->post(
                $this->api_address . $path, [
                'multipart' =>
                    $body
            ]);
        } catch (ServerException $server_exception) {
            $this->api_response = $server_exception;
        } catch (ClientException $client_exception) {
            $this->api_response = $client_exception;
        } catch (BadResponseException $response_exception) {
            $this->api_response = $response_exception;
        }

        return $this->api_response;

    }

    public function getEmail()
    {
        if (isset($_SESSION['email'])) {
            return $_SESSION['email'];
        }
    }
}