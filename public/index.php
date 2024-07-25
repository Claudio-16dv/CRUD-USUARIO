<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;

// Cria uma nova instância do aplicativo Slim
$app = AppFactory::create();

// Configuração do Eloquent para conexão com o MySQL
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',  // Endereço do servidor MySQL
    'database'  => 'cadastro',   // Nome do banco de dados
    'username'  => 'usuario',    // Usuário do banco de dados
    'password'  => '1111',      // Senha do banco de dados
    'charset'   => 'utf8',       // Charset usado na conexão
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',           // Prefixo para as tabelas (se necessário)
]);

// Define a instância do Capsule como global para usar em toda a aplicação
$capsule->setAsGlobal();
// Inicia o Eloquent ORM
$capsule->bootEloquent();

// Inclui as rotas definidas no arquivo src/Routes/web.php
require __DIR__ . '/../src/Routes/web.php';

// Executa o aplicativo Slim
$app->run();
