<?php

namespace App\Controllers\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Services\AuthService;

class AuthController {

    protected $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function register(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        
        $result = $this->authService->register($data, $uploadedFiles);

        if ($result['status'] === 302) {
            return $response->withHeader('Location', '/login.html')->withStatus(302);
        }

        $response->getBody()->write(json_encode(['error' => $result['error']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($result['status']);
    }

    public function login(Request $request, Response $response) {
        $data = $request->getParsedBody();
        
        $result = $this->authService->login($data);

        if ($result['status'] === 302) {
            return $response->withHeader('Location', '/home.php')->withStatus(302);
        }

        $response->getBody()->write(json_encode(['error' => $result['error']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($result['status']);
    }
}

