<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\Middleware\BodyParsingMiddleware;
use Slim\Exception\HttpNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app = AppFactory::create();

// Adiciona o middleware para parsing do corpo da requisição
$app->addBodyParsingMiddleware();  

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

// Configura o middleware de erro
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$errorMiddleware->setErrorHandler(
    HttpNotFoundException::class,
    function (Request $request, Throwable $exception, bool $displayErrorDetails) use ($app) {
        $response = $app->getResponseFactory()->createResponse();
        $response->getBody()->write('Rota não encontrada!');
        return $response->withStatus(404);
    }
);

// Inclui as rotas
require __DIR__ . '/../src/Routes/web.php';

// Executa o aplicativo Slim
$app->run();
