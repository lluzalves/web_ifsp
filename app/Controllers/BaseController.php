<?php

namespace App\Controllers;

use Interop\Container\ContainerInterface;
use GuzzleHttp\Client;
use Slim\Views\Twig as View;
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
    protected $api_address = 'http://192.168.0.15/slim_app/public';

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

    public function basicAuthRequest($body)
    {

        $credentials = base64_encode($body['email'].':'.$body['password']);
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

    public function requestSignInPostWithParams($path, $body)
    {
        try {
            $this->api_response = $this->client->post(
                $this->api_address . $path, [
                'form_params' => $body
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