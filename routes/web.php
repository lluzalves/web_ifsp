<?php

use App\Controllers\DocumentController;
use App\Controllers\HomeController;
use \App\Controllers\AuthController;

$app->group('/', function () {
    $this->get('', HomeController::class . ':index')->setName('home');
});

$app->group("/auth", function () {

    $this->get('/logout', AuthController::class . ':logout');

    $this->get('/login', AuthController::class . ':getSignIn')->setName('auth.signin');
    $this->post('/login', AuthController::class . ':postSignIn');

    $this->get('/register', AuthController::class . ':getSignUp')->setName('auth.signup');;
    $this->post('/register', AuthController::class . ':postSignUp');

    $this->get('/recover', AuthController::class . ':recoverCredentials')->setName('auth.recover');
    $this->post('/recover', AuthController::class . ':postSignInRecover');

});

$app->group('/document', function () {
    $this->get('',DocumentController::class . ':showAddForm')->setName('document.form');
    $this->get('{document_id}', DocumentController::class . ':getDocument')->setName('document.details');
    $this->post('', DocumentController::class . ':addDocument')->setName('document.add');
});
