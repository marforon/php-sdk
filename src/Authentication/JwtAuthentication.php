<?php

namespace Locardi\PhpSdk\Authentication;

use Locardi\PhpSdk\Exception\AuthenticationException;
use Locardi\PhpSdk\HttpClient\Client;
use Locardi\PhpSdk\Authentication\JwtAuthentication\TokenStorage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Request;

class JwtAuthentication
{
    const KEY = 'jtw_token';

    private $httpClient;

    private $authEndpoint;

    private $username;

    private $password;

    private $tokenStorage;

    public function __construct(Client $httpClient, $authEndpoint, $username, $password, TokenStorageInterface $tokenStorage)
    {
        $this->httpClient = $httpClient;
        $this->authEndpoint = $authEndpoint;
        $this->username = $username;
        $this->password = $password;
        $this->tokenStorage = $tokenStorage;
    }

    public function getToken()
    {
        $token = $this
            ->tokenStorage
            ->read(self::KEY)
        ;
        if ($token) {
            return $token;
        }

        $data = array(
            '_username' => $this->username,
            '_password' => $this->password,
        );

        $data = json_encode($data);

        $request = new Request(
            array(), // query
            array(), // request
            array(), // attributes
            array(), //cookies
            array(), // files
            array(), // server
            $data
        );

        $request->setMethod(Request::METHOD_POST);

        $response = $this
            ->httpClient
            ->send($this->authEndpoint, $request)
        ;

        switch ($response->getStatusCode()) {
            case 200:
                $content = $response->getContent();
                break;
            default:
                throw new AuthenticationException(sprintf('Authentication error: %s', $response->getContent()));
        }

        $contentArray = json_decode($content, true);

        if (!isset($contentArray['success'])) {
            throw new AuthenticationException(sprintf('Auth response format broken. %s', $response->getContent()));
        }

        $token = $contentArray['token'];

        $this
            ->tokenStorage
            ->write(self::KEY, $token)
        ;

        return $token;
    }

    public function updateRequest(Request $request)
    {
        $request
            ->headers
            ->set('X-Access-Token', $this->getToken())
        ;
    }
}
