<?php

namespace SignInWithApple;

use Curl\Curl;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use UnexpectedValueException;

/**
 * Class IdentityToken
 *
 * @package       SignInWithApple
 *
 * @property-read string $iss
 * @property-read string $sub
 * @property-read string $aud
 * @property-read int    $exp
 * @property-read int    $iat
 * @property-read string $nonce
 * @property-read int    $nonce_supported
 * @property-read string $email
 * @property-read bool   $email_verified
 * @property-read int    $auth_time
 * @property-read string $at_hash
 *
 */
class IdentityToken
{
    /**
     * @var array
     */
    static $publicKeys;

    /**
     * @var object|null
     */
    private $data;

    public static function reloadPublicKeys(): void
    {
        $curl = new Curl();
        $curl->setDefaultJsonDecoder(true);
        $curl->get('https://appleid.apple.com/auth/keys');

        self::$publicKeys = JWK::parseKeySet($curl->getResponse());
    }

    public function __construct(string $idTokenString, ?string $nonce = null)
    {
        if (is_null(self::$publicKeys)) {
            self::reloadPublicKeys();
        }

        $this->data = JWT::decode($idTokenString, self::$publicKeys, ['RS256']);

        if (!is_null($nonce) && !empty($this->data->nonce_supported) && $this->data->nonce !== $nonce) {
            $this->data = null;
            throw new UnexpectedValueException('Invalid nonce');
        }
    }

    public function __get(string $name)
    {
        if (!$this->__isset($name)) {
            return null;
        }

        return $this->data->$name;
    }

    public function __isset(string $name): bool
    {
        return is_object($this->data) && property_exists($this->data, $name);
    }
}
