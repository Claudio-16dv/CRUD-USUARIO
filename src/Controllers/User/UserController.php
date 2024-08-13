<?php 
namespace App\Controllers\User;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\Services\UserService;


class UserController {
    
    protected $userService;

    public function __construct() {
        $this->userService = New UserService();
    }

    public function deleteUserById(Request $request, Response $response) {
    
        $result = $this->userService->deleteUserById($request);

        $response->getBody()->write(json_encode(['message' => $result['message'] ?? $result['error']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($result['status']);
    }

    public function updateUserById(Request $request, Response $response, $args) {
        $data = $request->getParsedBody();
        $id = $data['id'] ?? null;
        $bio = $data['bio'] ?? null;
        $uploadedFiles = $request->getUploadedFiles();
        $profileImage = $uploadedFiles['profile_image'] ?? null;
    
        $result = $this->userService->updateUserById($id, $bio, $profileImage);
    
        
        if ($result['status'] === 200) {
            return $response->withHeader('Location', '/home.php')->withStatus(302);
        }
    
        $response->getBody()->write(json_encode(['message' => $result['message'] ?? $result['error']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($result['status']);
    }

    public function getUserById(Request $request, Response $response){
        $data = $request->getParsedBody();
        $userId = $data['id'] ?? '';
        $result = $this->userService->getUserById($userId);

        $response->getBody()->write(json_encode($result['data'] ?? ['error' => $result['error']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($result['status']);
    }

}