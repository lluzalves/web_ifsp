<?php

namespace App\Controllers;

use Interop\Container\ContainerInterface;
use GuzzleHttp\Client;
use Slim\Views\Twig as View;

abstract class BaseController
{

    protected $container;
    protected $view;
    protected $client;
    protected $api_address = 'http://192.168.0.15/slim_app/public';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->view = $container->view;
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
        $api_response = $client->post(
            $this->api_address . $path, [
            'form_params' => $body
        ]);

        // var_dump($api_response->getBody()->getContents());

        return $api_response->getBody()->getContents();

    }
}