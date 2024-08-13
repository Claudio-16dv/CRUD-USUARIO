<?php 
namespace App\Services;

use Illuminate\Database\Capsule\Manager as Capsule;


class UserService{

    public function updateUserById($id, $bio, $profileImage) {
        if ($id === null) {
            return ['error' => 'ID do usuário não fornecido', 'status' => 400];
        }
    
        if ($bio === null && $profileImage === null) {
            return ['error' => 'É necessário fornecer a bio ou a imagem de perfil para atualizar', 'status' => 400];
        }
    
        $user = Capsule::table('users')->where('id', $id)->first();
        if (!$user) {
            return ['error' => 'Usuário não encontrado', 'status' => 404];
        }
    
        $updateData = [];
    
        if ($bio !== null) {
            $updateData['bio'] = $bio;
        }
    
        if ($profileImage && $profileImage->getError() === UPLOAD_ERR_OK) {
            $uploadDirectory = __DIR__ . '/../uploads';
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }

            $filename = sprintf('%s.%s', uniqid(), pathinfo($profileImage->getClientFilename(), PATHINFO_EXTENSION));
            $filepath = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;

            $profileImage->moveTo($filepath);
            $updateData['profile_image'] = $filename;

            if ($user->profile_image) {
                $oldFilepath = $uploadDirectory . DIRECTORY_SEPARATOR . $user->profile_image;
                if (file_exists($oldFilepath)) {
                    unlink($oldFilepath);
                }
            }
        }

        Capsule::table('users')->where('id', $id)->update($updateData);
    
        return ['message' => 'Perfil atualizado com sucesso', 'status' => 200];
    }


    public function deleteUserById($request) {
        $data = $request->getParsedBody();
        $id = $data['id'] ?? null;

        if (!$id) {
            return ['error' => 'ID do usuário não fornecido', 'status' => 400];
        }

        $user = Capsule::table('users')->where('id', $id)->first();
    
        if (!$user) {
            return ['error' => 'Usuário não encontrado', 'status' => 404];
        }
    
        Capsule::table('users')->where('id', $id)->delete();
    
        return ['message' => 'Usuário deletado com sucesso', 'status' => 200];
    }

    public function getUserById($id) {
        $user = Capsule::table('users')->where('id', $id)->first();
    
        if (!$user) {
            return ['error' => 'Usuário não encontrado', 'status' => 404];
        }
        $userArray = (array) $user;
    
        unset($userArray['password']);
    
        return ['data' => $userArray, 'status' => 200];
    }
    
}