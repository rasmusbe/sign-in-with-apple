<?php

namespace SignInWithApple;

use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;

class AccessToken
{
    /**
     * @var string
     */
    protected $refresh_token;

    /**
     * @var array
     */
    protected $id_token;

    public function __construct($appleResponse)
    {
        $this->refresh_token = $appleResponse->refresh_token;

        $this->id_token = $this->readIdToken($appleResponse->id_token);
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refresh_token;
    }

    public function getUserId(): string
    {
        return $this->id_token['sub'];
    }

    public function getEmail(): ?string {
        return $this->id_token['email'] ?? null;
    }

    /**
     * @return array
     */
    public function getIdToken(): array
    {
        return $this->id_token;
    }

    protected function readIdToken(string $id_token): array
    {
        $serializerManager = new JWSSerializerManager([
            new CompactSerializer(),
        ]);

        $jws = $serializerManager->unserialize($id_token);

        return json_decode($jws->getPayload(), true);
    }
}
