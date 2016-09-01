<?php

require_once __DIR__ . '/../vendor/autoload.php';

$tokenStorage = new \Locardi\PhpSdk\Authentication\JwtAuthentication\TokenStorage\FileTokenStorage(__DIR__);

$client = new \Locardi\PhpSdk\LocardiClient(array(
    'username' => 'my-username',
    'password' => 'my-password',
    'tokenStorage' => $tokenStorage,
    'endpoint' => 'http://127.0.0.1:8088/api/request', // optional
    'authEndpoint' => 'http://127.0.0.1:8087/api/login', // optional
));

$client->send(array(
    'request_type' => 'html_page',
    'ip_v4' => '8.8.8.8',
    'http_method' => 'POST',
    'timestamp' => date('c'),
    'user_agent' => 'Chrome 12307823498',
    'uri' => 'http://whatever.com?q=hello-world',
    'organization' => array(
        'id' => '56',
        'name' => 'Barclays',
    ),
    'user' => array(
        'id' => 'v76fjg',
        'name' => 'George Osborne',
        'type' => 'internal',
        'timezone' => 'Europe/London',
    ),
));
