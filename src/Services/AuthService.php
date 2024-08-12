<?php 
namespace App\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use Firebase\JWT\JWT;

class AuthService{
    private $secretKey = 'secreta';

    public function register($data, $uploadedFiles)
    {
        $name = $data['full_name'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $bio = $data['bio'] ?? '';
        
        $profileImage = $uploadedFiles['profile_image'] ?? null;

        if (empty($name) || empty($email) || empty($password) || empty($bio) || !$profileImage) {
            return ['error' => 'Todos os campos são obrigatórios', 'status' => 400];
        }

        if ($profileImage->getError() !== UPLOAD_ERR_OK) {
            return ['error' => 'Erro ao enviar a imagem', 'status' => 400];
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

        return ['message' => 'Usuário registrado com sucesso', 'status' => 302];
    }

    public function login($data)
    {
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = Capsule::table('users')->where('email', $email)->first();

        if (!$user || !password_verify($password, $user->password)) {
            return ['error' => 'Credenciais inválidas', 'status' => 401];
        }

        $token = JWT::encode(['sub' => $user->id], $this->secretKey, 'HS256');

        return ['token' => $token, 'status' => 302];
    }
}