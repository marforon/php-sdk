<?php

ignore_user_abort(true);
set_time_limit(0);

require_once __DIR__ . '/../vendor/autoload.php';

use Locardi\PhpSdk\Authentication\JwtAuthentication\TokenStorage\FileTokenStorage;
use Locardi\PhpSdk\LocardiClient;

$endpoint = 'http://127.0.0.1:8088';

$client1 = new LocardiClient(array(
    'debug' => true,
    'username' => 'everlution',
    'password' => 'everlution',
    'endpoint' => $endpoint,
    'tokenStorage' => new FileTokenStorage(__DIR__),
));
$client2 = new LocardiClient(array(
    'debug' => true,
    'username' => 'roxhillmedia',
    'password' => 'roxhillmedia',
    'endpoint' => $endpoint,
    'tokenStorage' => new FileTokenStorage(__DIR__ . '/client2'),
));

$faker = \Faker\Factory::create();

$ipPool = [
    '192.168.1.1',
    '192.168.1.2',
    '192.168.1.3',
    '192.168.1.4',
    '192.168.1.5',
    '192.168.1.6',
    $faker->ipv4,
    $faker->ipv4,
    $faker->ipv4,
    $faker->ipv4,
];

$httpMethods = [
    'GET',
    'POST',
];

$orgs = [
    [
        'name' => 'Google',
        'id' => '23f4',
        'users' => [
            [
                'id' => '32984h',
                'name' => 'user1',
                'type' => 'internal',
                'timezone' => 'Europe/London',
            ],
            [
                'id' => 'f0943ju2',
                'name' => 'user2',
                'type' => 'internal',
                'timezone' => 'Europe/London',
            ],
            [
                'id' => 'f749hbis',
                'name' => 'user3',
                'type' => 'internal',
                'timezone' => 'Europe/London',
            ],
        ],
    ],
    [
        'name' => 'Microsoft',
        'id' => '9hd23',
        'users' => [
            [
                'id' => '32984h',
                'name' => 'ms1',
                'type' => 'internal',
                'timezone' => 'Europe/London',
            ],
            [
                'id' => 'f0943ju2',
                'name' => 'ms2',
                'type' => 'external',
                'timezone' => 'Europe/Rome',
            ],
            [
                'id' => 'f749hbis',
                'name' => 'ms3',
                'type' => 'external',
                'timezone' => 'Europe/Rome',
            ],
        ],
    ],
    [
        'name' => 'Zoopla',
        'id' => '23xfrt',
        'users' => [
            [
                'id' => 'fu2yss',
                'name' => 'zoopla1',
                'type' => 'internal',
                'timezone' => 'Europe/London',
            ],
        ]
    ],
];

for ($i=0; $i<1000; $i++) {
    $client = rand(0, 1) == 0 ? $client1 : $client2;

    $start = microtime(true);

    $org = $orgs[rand(0, count($orgs) - 1)];
    $user = $org['users'][rand(0, count($org['users'])-1)];

    $client->send(array(
        'organization_user_request' => array(
            'request_type' => 'html_page',
            'ip_address' => $ipPool[rand(0, count($ipPool) - 1)],
            'http_method' => $httpMethods[rand(0, count($httpMethods) - 1)],
            'timestamp' => $faker->dateTimeBetween('-30 days', 'now')->format('c'),
            'user_agent' => $faker->userAgent,
            'uri' => $faker->url,
            'organization' => array(
                'id' => $org['id'],
                'name' => $org['name'],
            ),
            'user' => array(
                'id' => $user['id'],
                'name' => $user['name'],
                'type' => $user['type'],
                'timezone' => $user['timezone'],
            ),
        ),
    ));

    $total = microtime(true) - $start;
    dump($total);
}
