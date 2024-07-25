<?php

namespace App\Controllers\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as Capsule;
use Firebase\JWT\JWT;

class AuthController {
    private $secretKey = 'secreta'; // Substitua por uma chave secreta mais segura

    public function register(Request $request, Response $response, $args) {
        $data = $request->getParsedBody(); // Método PSR-7 para obter o corpo da requisição

        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            $response->getBody()->write(json_encode(['error' => 'Todos os campos são obrigatórios']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        Capsule::table('users')->insert([
            'name' => $name,
            'email' => $email,
            'password' => $passwordHash
        ]);

        $response->getBody()->write(json_encode(['message' => 'Usuário registrado com sucesso']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function login(Request $request, Response $response, $args) {
        $data = $request->getParsedBody(); // Método PSR-7 para obter o corpo da requisição

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = Capsule::table('users')->where('email', $email)->first();

        if (!$user || !password_verify($password, $user->password)) {
            $response->getBody()->write(json_encode(['error' => 'Credenciais inválidas']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = JWT::encode(['sub' => $user->id], $this->secretKey, 'HS256');

        $response->getBody()->write(json_encode(['token' => $token]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
