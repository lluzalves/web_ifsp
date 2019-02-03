<?php
use App\Controllers\HomeController;
use \App\Controllers\AuthController;

$app->group('/', function(){
    $this->get('', HomeController::class.':index')->setName('home');
});

$app->group("/auth", function(){

    $this->get('/logout', AuthController::class.':logout');

    $this->get('/login', AuthController::class.':getSignIn')->setName('auth.signin');
    $this->post('/login', AuthController::class.':postSignIn');

    $this->get('/register', AuthController::class.':getSignUp')->setName('auth.signup');;
    $this->post('/register', AuthController::class.':postSignUp');

});