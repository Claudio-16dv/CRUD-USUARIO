<?php

namespace App\Controllers\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as Capsule;
use Firebase\JWT\JWT;

class AuthController {
    private $secretKey = 'secreta';

    public function register(Request $request, Response $response) {
       
        $data = $request->getParsedBody();

        $name = $data['full_name'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $bio = $data['bio'] ?? '';
        
        $uploadedFiles = $request->getUploadedFiles();
        $profileImage = $uploadedFiles['profile_image'] ?? null;

        if (empty($name) || empty($email) || empty($password) || empty($bio) || !$profileImage) {
            $response->getBody()->write(json_encode(['error' => 'Todos os campos são obrigatórios']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if ($profileImage->getError() !== UPLOAD_ERR_OK) {
            $response->getBody()->write(json_encode(['error' => 'Erro ao enviar a imagem']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $directory = __DIR__ . '/../uploads';
        if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
        }

        $filename = sprintf('%s.%s', uniqid(), pathinfo($profileImage->getClientFilename(), PATHINFO_EXTENSION));
        $profileImage->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
    
       
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);


        Capsule::table('users')->insert([
            'full_name' => $name,
            'email' => $email,
            'password' => $passwordHash,
            'bio' => $bio,
            'profile_image' => $filename
        ]);

        $response->getBody()->write(json_encode(['message' => 'Usuário registrado com sucesso']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function login(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();

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

