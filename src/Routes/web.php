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

$app->get('/page/{name}', function ($request, $response, $args) {
    $page = $args['name'];

    $file = __DIR__ . '/../public/' . $page . '.html';

    if (file_exists($file)) {
        $response->getBody()->write(file_get_contents($file));
        return $response->withHeader('Content-Type', 'text/html');
    } else {
        return $response->withStatus(404)->write('Page not found');
    }
});


