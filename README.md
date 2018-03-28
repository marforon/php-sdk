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
    'timeout' => 10, // OPTIONAL number of senconds before curl timeout (default is 30 seconds) 
));

$client->send(array(
    'organization_user_request' => array(
        // this is the type of the page that the user has opened (html_page,login,login_failed,login_failed_wrong_username,login_failed_wrong_password,download)
        'request_type' => OrganizationUserRequestApi::REQUEST_TYPE_HTML_PAGE,
        // the IP Address of the user
        'ip_address' => '8.8.8.8',
        // the HTTP method used by the user (GET,POST,OPTION,PUT,DELETE)
        'http_method' => 'GET',
        // the timestamp when the user request was generated
        'timestamp' => date('c'),
        // the user agent used by the user
        'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.110 Safari/537.36',
        // the full address requested by the user (including query string, anchors etc)
        'uri' => 'https://www.getlocardi.com/?param1=value1&param2=value2',
        // the organization in which the user is
        'organization' => array(
            'id' => '123', // note that this is a string
            'name' => 'Organization1',
        ),
        // some details on the user
        'user' => array(
            'id' => '123', // note that this is a string
            'name' => 'User1', // you can fill this field with the username,email or any other unique id
            // this is the user type and represents whether the user is internal (like an admin/editor account) or external (like a normal user)
            'type' => OrganizationUserRequestApi::USER_TYPE_INTERNAL,
            // the timezone where the user lives
            'timezone' => 'Europe/London',
        ),
    ),
));

```

## Request Types

You can send requests with different types base on what you need to track:

- `OrganizationUserRequestApi::REQUEST_TYPE_HTML_PAGE` when the user visits a normal web page
- `OrganizationUserRequestApi::REQUEST_TYPE_LOGIN` when the user is attempting the login
- `OrganizationUserRequestApi::REQUEST_TYPE_LOGIN_FAILED` when the login attempt has failed for any reason
- `OrganizationUserRequestApi::REQUEST_TYPE_LOGIN_FAILED_WRONG_USERNAME` when the login attempt failed due to a username/email that are not in your database
- `OrganizationUserRequestApi::REQUEST_TYPE_LOGIN_FAILED_WRONG_PASSWORD` when the login attempt failed due to a wrong password
- `OrganizationUserRequestApi::REQUEST_TYPE_DOWNLOAD` when the user has downloaded a file

## User types

You may have the need to differentiate between your own users' accounts (such as admin accounts etc) and your clients' accounts. 

- `OrganizationUserRequestApi::USER_TYPE_INTERNAL` these are users that you consider internal to your organization (i.e. admins, editors, etc)
- `OrganizationUserRequestApi::USER_TYPE_EXTERNAL` these are users that are using your service (actual clients that you want to track)

## Token Storage

The Locardi API implements a JSON Web Token authentication system.

The SDK requires a way to store the JSON Web Token, for this reason we provide
a `TokenStorageInterface` that you can use to save the token in any storage system.

We provide a simple `FileTokenStorage` class that saves the token on the filesystem but you can write your own adapter simply using the `TokenStorageInterface`. 
