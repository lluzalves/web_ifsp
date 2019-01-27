<?php
use App\Controllers\HomeController;
$app->group('/', function(){
    $this->get('', HomeController::class.':index');
    $this->get('/login', HomeController::class.':login');
    $this->get('/logout', HomeController::class.':logout');
    $this->get('/register', HomeController::class.':register');
});