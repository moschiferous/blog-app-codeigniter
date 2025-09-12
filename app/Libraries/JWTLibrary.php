<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTLibrary
{
    private $key;
    private $algorithm = 'HS256';

    public function __construct()
    {
        $this->key = getenv('JWT_SECRET_KEY') ?: 'your-secret-key-change-this';
    }

    public function generateToken($payload)
    {
        $issuedAt = time();
        $expire = $issuedAt + 3600; // Token expires in 1 hour

        $token = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'data' => $payload
        ];

        return JWT::encode($token, $this->key, $this->algorithm);
    }

    public function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->key, $this->algorithm));
            return (array) $decoded->data;
        } catch (\Exception $e) {
            return false;
        }
    }
}