# Locardi PHP SDK

## Installation

```bash
composer require locardi/php-sdk "dev-master"
```

## Usage

```php
use Locardi\PhpSdk\Authentication\JwtAuthentication\TokenStorage\FileTokenStorage;
use Locardi\PhpSdk\LocardiClient;
use Locardi\PhpSdk\Api\OrganizationUserRequestApi;

$client = new LocardiClient(array(
    'debug' => true,
    'username' => 'myusername',
    'password' => 'mypassword',
    'endpoint' => '', // this is optional and overrides the API endpoing (used for sandbox)
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
```

## Token Storage

The Locardi API implements a JSON Web Token authentication system.

The SDK requires a way to store the JSON Web Token, for this reason we provide
a `TokenStorageInterface` that you can use to save the token in any storage system.

We provide a simple `FileTokenStorage` class that saves the token on the filesystem but you can write your own adapter simply using the `TokenStorageInterface`. 
