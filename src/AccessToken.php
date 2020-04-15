<?php

namespace SignInWithApple;

use Curl\Curl;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

class AccessToken
{
    /**
     * @var string
     */
    protected $access_token;

    /**
     * @var string|null
     */
    protected $refresh_token;

    /**
     * @var object|null
     */
    protected $id_token;

    /**
     * @var array|null
     */
    protected $user;

    public function __construct($appleResponse)
    {
        $this->access_token = $appleResponse->access_token;

        $this->refresh_token = $appleResponse->refresh_token;

        $this->id_token = $this->readIdToken($appleResponse->id_token);

        $this->user = isset($_POST['user']) ? json_decode($_POST['user'], true) : null;
    }

    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): ?string
    {
        return $this->refresh_token;
    }

    public function getUserId(): ?string
    {
        return $this->id_token->sub ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->id_token->email ?? null;
    }

    /**
     * @return object
     */
    public function getIdToken(): ?object
    {
        return $this->id_token;
    }

    public function getName(): ?string
    {
        $firstName = $this->getFirstName() ?? '';
        $lastName  = $this->getLastName() ?? '';

        if (empty($this->user['name'])) {
            return null;
        }

        return trim($firstName . ' ' . $lastName);
    }

    public function getFirstName(): ?string
    {
        return $this->user['name']['firstName'] ?? null;
    }

    public function getLastName(): ?string
    {
        return $this->user['name']['lastName'] ?? null;
    }

    protected function readIdToken(?string $id_token): ?object
    {
        if (is_null($id_token)) {
            return null;
        }

        $curl = new Curl();
        $curl->setDefaultJsonDecoder(true);
        $curl->get('https://appleid.apple.com/auth/keys');

        $keys = JWK::parseKeySet($curl->getResponse());

        return JWT::decode($id_token, $keys, ['RS256']);
    }
}
