<?php 
namespace App\Controllers\User;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as Capsule;


class UserController {

    private $secretKey = 'secreta';

    public function deleteUserById(Request $request, Response $response) {
        $data = $request->getParsedBody();
    
        $id = $data['id'] ?? null;
    
        if (!$id) {
            $response->getBody()->write(json_encode(['error' => 'ID do usuário não fornecido']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
        $user = Capsule::table('users')->where('id', $id)->first();
    
        if (!$user) {
            $response->getBody()->write(json_encode(['error' => 'Usuário não encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    
        Capsule::table('users')->where('id', $id)->delete();
    
        $response->getBody()->write(json_encode(['message' => 'Usuário deletado com sucesso']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function updateUserById(Request $request, Response $response, $args){
        $data = $request->getParsedBody();
    
        $id = $data['id'] ?? null;
        $bio = $data['bio'] ?? null;
        $profileImage = $data['profile_image'] ?? null;

    
        if ($id=== null) {
            $response->getBody()->write(json_encode(['error' => 'ID do usuário não fornecido']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if ($bio === null && $profileImage === null) {
            $response->getBody()->write(json_encode(['error' => 'É necessário fornecer a bio ou a imagem de perfil para atualizar']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
        $user = Capsule::table('users')->where('id', $id)->first();
        if (!$user) {
            $response->getBody()->write(json_encode(['error' => 'Usuário não encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $updateData = [];
        if ($bio !== null) {
        $updateData['bio'] = $bio;
    }
    if ($profileImage && $profileImage->getError() === UPLOAD_ERR_OK) {
        
        $uploadDirectory = __DIR__ . '/../uploads/';
        $filename = $profileImage->getClientFilename();
        $filepath = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;

        
        $profileImage->moveTo($filepath);

        
        $updateData['profile_image'] = $filename;
    }

        Capsule::table('users')->where('id', $id)->update($updateData);

        $response->getBody()->write(json_encode(['message' => 'Perfil atualizado com sucesso']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getUserById(Request $request, Response $response){
        $data = $request->getParsedBody();

        
        $userId = $data['id'] ?? '';

        
        $user = Capsule::table('users')->where('id', $userId)->first();

        
        if (!$user) {
            
            $response->getBody()->write(json_encode(['error' => 'Usuário não encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

}