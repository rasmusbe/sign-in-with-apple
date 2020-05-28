<?php

use SignInWithApple\Auth;

require_once '../vendor/autoload.php';

$config = json_decode(
    file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'config.json', false),
    true
);

$auth = new Auth($config, __DIR__ . DIRECTORY_SEPARATOR . 'AuthKey.p8');

echo "<pre>";
try {
    if (!empty($_REQUEST['refresh']) && isset($_REQUEST['token'])) {
        $token = $auth->getAccessToken($_REQUEST['token'], true);
    } elseif (isset($_REQUEST['code'])) {
        $token = $auth->getAccessToken($_REQUEST['code']);
    } else {
        throw new UnexpectedValueException('Missing code or refresh token');
    }
} catch (Exception $e) {
    printf('error: "%s"' . PHP_EOL, $e->getMessage());
}

if ($token) {
    $refreshToken = $token->getRefreshToken() ?? $_REQUEST['token'] ?? null;
    printf('access token: "%s"' . PHP_EOL, $token->getAccessToken());
    printf('user id: "%s" (only available on login)' . PHP_EOL, $token->getUserId());
    printf('user email: "%s" (only available on login)' . PHP_EOL, $token->getEmail());
    printf('user name: "%s" (only available on FIRST login)' . PHP_EOL, $token->getFullName() ?? '');
    printf('refresh token: "%s"' . PHP_EOL, $refreshToken ?? '');
    printf('id_token: (only available on login)' . PHP_EOL . '%s', print_r($token->getIdToken(), true));
}

if ($refreshToken) {
    printf('<a href="auth.php?refresh=1&token=%s">Refresh</a>' . PHP_EOL, $refreshToken);
}

echo '<a href="/">Log in again</a>';

