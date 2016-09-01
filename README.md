# Locardi PHP SDK

## Installation

```bash
composer require locardi/php-sdk "dev-master"
```

## Usage

```php
use Locardi\PhpSdk\Authentication\JwtAuthentication\TokenStorage\FileTokenStorage;
use Locardi\PhpSdk\LocardiClient;

$tokenStorage = new FileTokenStorage(__DIR__);

$client = new LocardiClient(array(
    'username' => 'my-username',
    'password' => 'my-password',
    'tokenStorage' => $tokenStorage,
));

$client->send(array(
    'request_type' => 'html_page',
    'ip_v4' => '8.8.8.8',
    'http_method' => 'POST',
    'timestamp' => date('c'),
    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
    'uri' => 'http://whatever.com?q=hello-world', // the URI that the user has visited
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
```

## Token Storage

The Locardi API implements a JSON Web Token authentication system.

The SDK requires a way to store the JSON Web Token, for this reason we provide
a `TokenStorageInterface` that you can use to save the token in any storage system.

We provide a simple `FileTokenStorage` class that saves the token on the filesystem.
