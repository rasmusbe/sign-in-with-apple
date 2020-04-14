<?php

namespace SignInWithApple;

use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\ES256;
use Jose\Easy\Build;

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
        $this->privateKey = JWKFactory::createFromKeyFile(
            $privateKeyPath
        );
    }

    public function generateToken(): string
    {
        return Build::jws()
            ->iss($this->config['team_id'] ?? '')
            ->sub($this->config['client_id'] ?? '')
            ->iat(time())
            ->exp(strtotime('+5 months'))
            ->claim('aud', 'https://appleid.apple.com')
            ->alg((new ES256())->name())
            ->sign($this->privateKey);
    }
}
