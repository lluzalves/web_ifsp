<?php

namespace App\Controllers;

use Interop\Container\ContainerInterface;
use GuzzleHttp\Client;
use Slim\Views\Twig as View;

abstract class BaseController
{

    protected $container;
    protected $view;
    protected $api_address = 'https://56e80ec2.ngrok.io/slim_app/public';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->view = $container->view;
    }

    public function request()
    {
        $client = new Client();
        $api_request = $client->request(
            'GET',
            $this->api_address
        );
        return $api_request;
    }
}