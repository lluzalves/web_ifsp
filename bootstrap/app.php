<?php

use App\Controllers\AuthController;
use App\Controllers\DocumentController;
use App\Controllers\HomeController;
use App\Controllers\NotificationController;
use App\Controllers\UserController;
use App\Middleware\BaseMiddleware;
use App\Middleware\ValidationErrors;
use  App\Validation\Validator;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

session_start();
require __DIR__ . '/../vendor/autoload.php';
$app = new App([
    'settings' => [
        'displayErrorDetails' => true,
    ]
]);
//retrieve container
$container = $app->getContainer();

$app->add(function ($request, $response, $next) {
    $response = $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
    $response = $next ($request, $response);
    return $response;
});

//register component on container
$container ['view'] = function ($container) {

    $view = new Twig(__DIR__ . '/../resources/views', [
        'cache' => false,
    ]);
    //instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = Uri::createFromEnvironment(new Environment($_SERVER));
    $view->addExtension(new TwigExtension($router, $uri));
    return $view;
};

$container['validator'] = function ($container) {
    return new Validator();
};

$container['HomeController'] = function ($container) {
    return new HomeController($container);
};

$container['AuthController'] = function ($container) {
    return new AuthController($container);
};

$container['DocumentController'] = function ($container) {
    return new DocumentController($container);
};

$container['UserController'] = function ($container) {
    return new UserController($container);
};

$container['NotificationController'] = function ($container) {
    return new NotificationController($container);
};

$app->add(new BaseMiddleware($container));
$app->add(new ValidationErrors($container));

require __DIR__ . '/../routes/web.php';
