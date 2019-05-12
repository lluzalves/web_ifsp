<?php

use App\Controllers\DocumentController;
use App\Controllers\HomeController;
use \App\Controllers\AuthController;
use App\Controllers\NotificationController;
use App\Controllers\UserController;
use App\Controllers\EdictController;

$app->group('/', function () {
    $this->get('', HomeController::class . ':index')->setName('home');
});

$app->group("/auth", function () {

    $this->get('/logout', AuthController::class . ':logout')->setName('auth.logout');

    $this->get('/login', AuthController::class . ':getSignIn')->setName('auth.signin');
    $this->post('/login', AuthController::class . ':postSignIn');

    $this->get('/register', AuthController::class . ':getSignUp')->setName('auth.signup');;
    $this->post('/register', AuthController::class . ':postSignUp');

    $this->get('/recover', AuthController::class . ':recoverCredentials')->setName('auth.recover');
    $this->post('/recover', AuthController::class . ':restartCredentials');

    $this->get('/terms', AuthController::class . ':terms')->setName('auth.terms');
});

$app->group('/document', function () {
    $this->get('', DocumentController::class . ':showAddForm')->setName('document.form');
    $this->get('/{document_id}', DocumentController::class . ':requestDocument')->setName('document.details');
    $this->get('/{document_id}/attachment', DocumentController::class . ':requestDocumentAttachment')->setName('document.download');
    $this->post('', DocumentController::class . ':addDocument')->setName('document.add');
    $this->get('/validate/{document_id}', DocumentController::class . ':validateDocument')->setName('document.validate');
    $this->get('{user_id}/{document_id}/edit', DocumentController::class . ':updateSessionDocument')->setName('document.edit');
    $this->get('/user/{user_id}', DocumentController::class . ':updateSessionUserId')->setName('documentbyadmin.add');
    $this->get('/{document_id}/delete', DocumentController::class . ':delete')->setName('delete');
});

$app->group('/users', function () {
    $this->get('/all/edict/{edict_id}', UserController::class . ':requestUsers')->setName('users.all');
    $this->get('/{email}', UserController::class . ':requestUserDetails')->setName('user.details');
    $this->get('/{email}/attachments', UserController::class . ':requestUserAttachments')->setName('user.download');
    $this->get('/{email}/delete', UserController::class . ':delete')->setName('user.delete');
    $this->get('/{user_id}/notify', UserController::class . ':notify')->setName('user.notify');
    $this->post('/filter/user', UserController::class . ':filter')->setName('user.filter');
});

$app->group('/edict', function () {
    $this->get('', EdictController::class . ':showAddForm')->setName('edict.form');
    $this->post('', EdictController::class . ':addEdict')->setName('edict.add');
    $this->get('/all', EdictController::class . ':requestEdicts')->setName('edicts.all');
    $this->get('/details/{edict_id}', EdictController::class . ':requestEdictDetails')->setName('edict.details');
});

$app->group('/notification', function () {
    $this->post('/sendnotification', NotificationController::class . ':sendNotification')->setName('notification.create');
});