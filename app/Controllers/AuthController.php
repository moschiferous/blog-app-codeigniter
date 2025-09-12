<?php

namespace App\Controllers;

use App\Models\User;
use App\Libraries\JWTLibrary;
use CodeIgniter\API\ResponseTrait;

class AuthController extends BaseController
{
    use ResponseTrait;

    protected $userModel;
    protected $jwt;

    public function __construct()
    {
        $this->userModel = new User();
        $this->jwt = new JWTLibrary();
        helper(['form', 'url']);
    }

    public function register()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]|max_length[200]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'password' => $this->request->getVar('password')
        ];

        $this->userModel->save($data);

        return $this->respondCreated([
            'status' => 201,
            'message' => 'User registered successfully'
        ]);
    }

    public function login()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $this->userModel->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Invalid email or password');
        }

        $token = $this->jwt->generateToken([
            'user_id' => $user['id'],
            'email' => $user['email']
        ]);

        // Simpan token ke database
        $this->userModel->updateToken($user['id'], $token);

        return $this->respond([
            'status' => 200,
            'message' => 'Login successful',
            'token' => $token
        ]);
    }

    public function logout()
    {
        $token = $this->getBearerToken();

        if ($token) {
            $user = $this->userModel->getUserByToken($token);
            if ($user) {
                $this->userModel->updateToken($user['id'], null);
            }
        }

        return $this->respond([
            'status' => 200,
            'message' => 'Logout successful'
        ]);
    }

    private function getBearerToken()
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (!empty($header) && preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}