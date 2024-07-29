<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\Middleware\BodyParsingMiddleware;

$app = AppFactory::create();
$app->addBodyParsingMiddleware();  // Adiciona o middleware para parsing do corpo da requisição

// Configuração do Eloquent para conexão com o MySQL
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'cadastro',
    'username'  => 'root',
    'password'  => '1111',
    'charset'   => 'utf8',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Inclui as rotas
require __DIR__ . '/../src/Routes/web.php';


// Executa o aplicativo Slim
$app->run();
