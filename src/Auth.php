<?php

namespace SignInWithApple;

use Curl\Curl;
use ErrorException;
use RuntimeException;

class Auth
{

    /**
     * @var ClientSecret
     */
    protected $clientSecret;

    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config, string $privateKeyPath)
    {
        $this->config       = $config;
        $this->clientSecret = new ClientSecret($config, $privateKeyPath);
    }

    public function loginUrl(): string
    {
        $state = bin2hex(random_bytes(5));

        $queryString = http_build_query([
            'response_type' => 'code id_token',
            'response_mode' => 'form_post',
            'client_id'     => $this->config['client_id'],
            'redirect_uri'  => $this->config['redirect_uri'],
            'state'         => $state,
            'scope'         => $this->config['scope'],
        ], null, '&', PHP_QUERY_RFC3986);

        return 'https://appleid.apple.com/auth/authorize?' . $queryString;
    }

    public function getAccessToken(string $code, bool $refreshToken = false): AccessToken
    {
        $clientSecret = $this->clientSecret->generateToken();

        $curl = new Curl();
        $curl->setHeader('content-type', 'application/x-www-form-urlencoded');

        $payload = [
            'redirect_uri'  => $this->config['redirect_uri'],
            'client_id'     => $this->config['client_id'],
            'client_secret' => $clientSecret,
            'scope'         => $this->config['scope'],
        ];

        if ($refreshToken) {
            $payload['grant_type']    = 'refresh_token';
            $payload['refresh_token'] = $code;
        } else {
            $payload['grant_type'] = 'authorization_code';
            $payload['code']       = $code;
        }

        try {
            $payload = $curl->buildPostData($payload);
        } catch (ErrorException $e) {
            throw new RuntimeException('Unable to build payload', E_USER_ERROR, $e);
        }

        $curl->post('https://appleid.apple.com/auth/token', $payload);

        if ($curl->error) {
            throw new RuntimeException(
                sprintf('Got %s when getting access token',
                    $curl->response->error ?? 'unknown error'
                ),
                E_USER_ERROR
            );
        }
        $response = $curl->response;

        return new AccessToken($response);
    }
}
