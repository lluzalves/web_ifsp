<?php
use App\Controllers\HomeController;
use \App\Controllers\AuthController;

$app->group('/', function(){
    $this->get('', HomeController::class.':index');
});

$app->group("/auth", function(){

    $this->get('/logout', AuthController::class.':logout');

    $this->get('/login', AuthController::class.':getSignIn');

    $this->get('/register', AuthController::class.':getSignUp')->setName('auth.signup');;
    $this->post('/register', AuthController::class.':postSignUp');

});