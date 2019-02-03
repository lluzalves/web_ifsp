<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails'  => true,
    ]
]);
//retrive container
$container = $app->getContainer();

//api token access

//$container['token'] = function(){
//    return
//};

//register component on container
$container ['view'] = function ($container){

    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views',[
        'cache' => false,
    ]);
    //instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension( new \Slim\Views\TwigExtension($router,$uri));
    return $view;
};

$container['validator'] = function($container){
    return new App\Validation\Validator;
};

$container['HomeController'] = function ($container){
    return new \App\Controllers\HomeController($container);
};

$container['AuthController'] = function ($container){
    return new \App\Controllers\AuthController($container);
};

$app->add(new \App\Middleware\ValidationErrors($container));
$app->add(new \App\Middleware\PersistInput($container));


require __DIR__.'/../routes/web.php';
