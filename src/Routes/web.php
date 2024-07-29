<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\Auth\AuthController;

$app->group('/auth', function (RouteCollectorProxy $group) {
    $group->post('/register', [AuthController::class, 'register']);
    $group->post('/login', [AuthController::class, 'login']);
});



