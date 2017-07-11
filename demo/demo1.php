<?php

ignore_user_abort(true);
set_time_limit(0);

require_once __DIR__ . '/../vendor/autoload.php';

use Locardi\PhpSdk\Authentication\JwtAuthentication\TokenStorage\FileTokenStorage;
use Locardi\PhpSdk\LocardiClient;
use Locardi\PhpSdk\Api\OrganizationUserRequestApi;

$client = new LocardiClient(array(
    'debug' => true,
    'username' => 'michael_test2',
    'password' => 'michael_test2',
    'tokenStorage' => new FileTokenStorage(__DIR__),
));

$client->send(array(
    'organization_user_request' => array(
        'request_type' => OrganizationUserRequestApi::REQUEST_TYPE_HTML_PAGE,
        'ip_address' => '8.8.8.8',
        'http_method' => 'GET',
        'timestamp' => date('c'),
        'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.110 Safari/537.36',
        'uri' => 'https://www.getlocardi.com/?param1=value1&param2=value2',
        'organization' => array(
            'id' => '123', // note that this is a string
            'name' => 'Organization1',
        ),
        'user' => array(
            'id' => '123', // note that this is a string
            'name' => 'User1', // you can fill this field with the username,email or any other unique id
            'type' => OrganizationUserRequestApi::USER_TYPE_INTERNAL,
            'timezone' => 'Europe/London',
        ),
    ),
));
