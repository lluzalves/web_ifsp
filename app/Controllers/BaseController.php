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

    public function requestPostWithParams($path, $body)
    {
        $client = new Client();
        try {
            $api_response = $client->post(
                $this->api_address . $path, [
                'form_params' => $body
            ]);
        } catch (ServerException $server_exception) {
            $api_response = $server_exception;
        } catch (ClientException $client_exception) {
            $api_response = $client_exception;
        } catch (BadResponseException $response_exception) {
            $api_response = $response_exception;
        }

        return $api_response;

    }
}