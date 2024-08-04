<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\Auth\AuthController;
use App\Controllers\User\UserController;

$app->group('/auth', function (RouteCollectorProxy $group) {
    $group->post('/register', [AuthController::class, 'register']);
    $group->post('/login', [AuthController::class, 'login']);
});

$app->group('/user', function(RouteCollectorProxy $group){
    $group->post('/deleteUserById', [UserController::class, 'deleteUserById']);
    $group->post('/updateUserById', [UserController::class, 'updateUserById']);
    $group->post('/getUserById', [UserController::class, 'getUserById']);
});



