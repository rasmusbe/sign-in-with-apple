<?php

namespace SignInWithApple;

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
     * @var IdentityToken|null
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

        $this->id_token = isset($appleResponse->id_token) ? new IdentityToken($appleResponse->id_token) : null;

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
        return $this->getIdToken()->sub ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->getIdToken()->email ?? null;
    }

    public function getIdToken(): ?IdentityToken
    {
        return $this->id_token;
    }

    public function getFullName(): ?string
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
}
