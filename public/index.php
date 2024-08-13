<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\Middleware\BodyParsingMiddleware;
use Slim\Exception\HttpNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Dotenv\Dotenv;

//requireonce dirname(DIR, 1) . '/vendor/autoload.php';
$dotenv = Dotenv::createImmutable(dirname(__DIR__."/public"));
$dotenv->load();
$app = AppFactory::create();

// Adiciona o middleware para parsing do corpo da requisição
$app->addBodyParsingMiddleware();  

// Configuração do Eloquent para conexão com o MySQL
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => $_ENV['DRIVER'],
    'host'      => $_ENV['HOST'],
    'database'  => $_ENV['DATABASE'],
    'username'  => $_ENV['USERNAME'],
    'password'  => $_ENV['PASSWORD'],
    'charset'   => $_ENV['CHARSET'],
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
