<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\Auth\authController;

$app->group('/auth', function (RouteCollectorProxy $group) {
    $group->post('/register', authController::class . ':register');
    $group->post('/login', authController::class . ':login');
});
?>