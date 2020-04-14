<?php

use SignInWithApple\Auth;

require_once '../vendor/autoload.php';

$config = json_decode(
    file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'config.json', false),
    true
);

$auth = new Auth($config, __DIR__ . DIRECTORY_SEPARATOR . 'AuthKey.p8');

$authCode = $_POST['code'];
$user     = isset($_POST['user']) ? json_decode($_POST['user'], true) : null;

echo "<pre>";
printf('authcode: "%s"' . PHP_EOL, $authCode);

$refreshedToken = $auth->getAccessToken($authCode);
printf('user id: "%s"' . PHP_EOL, $refreshedToken->getUserId());
printf('user email: "%s"' . PHP_EOL, $refreshedToken->getEmail());
printf('user name: "%s" (only available on first login)' . PHP_EOL, implode(' ', $user['name'] ?? []));
