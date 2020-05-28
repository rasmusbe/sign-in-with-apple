<?php

namespace SignInWithApple;

use Firebase\JWT\JWT;

class ClientSecret
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var JWK
     */
    protected $privateKey;

    public function __construct(array $config, string $privateKeyPath)
    {
        $this->config     = $config;
        $this->privateKey = file_get_contents($privateKeyPath);
    }

    public function generateToken(): string
    {
        return JWT::encode([
            'iss' => $this->config['team_id'],
            'sub' => $this->config['client_id'],
            'iat' => time(),
            'exp' => strtotime('+5 months'),
            'aud' => 'https://appleid.apple.com',
        ], $this->privateKey, 'ES256');
    }
}
