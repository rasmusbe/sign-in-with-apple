<?php

use SignInWithApple\Auth;

require_once '../vendor/autoload.php';

$config = json_decode(
    file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'config.json', false),
    true
);

$loginUrl = (new Auth($config, __DIR__ . DIRECTORY_SEPARATOR . 'AuthKey.p8'))->loginUrl();

printf('<a href="%s">Log in </a>', $loginUrl);
