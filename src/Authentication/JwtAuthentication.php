<?php

namespace Locardi\PhpSdk\Authentication;

use Locardi\PhpSdk\Api\AuthApi;
use Locardi\PhpSdk\Exception\AuthenticationException;
use Locardi\PhpSdk\Authentication\JwtAuthentication\TokenStorage\TokenStorageInterface;
use Locardi\PhpSdk\HttpClient\CurlClient;
use Locardi\PhpSdk\Serializer\JsonSerializer;
use Symfony\Component\HttpFoundation\Request;

class JwtAuthentication
{
    const HTTP_HEADER_TOKEN = 'X-Access-Token';

    const KEY = 'jtw_token';

    private $client;

    private $endpoint;

    private $api;

    private $username;

    private $password;

    private $jsonSerializer;

    private $tokenStorage;

    public function __construct(
        CurlClient $client,
        $endpoint,
        AuthApi $api,
        $username,
        $password,
        JsonSerializer $jsonSerializer,
        TokenStorageInterface $tokenStorage
    ) {
        $this->client = $client;
        $this->endpoint = $endpoint;
        $this->api = $api;
        $this->username = $username;
        $this->password = $password;
        $this->jsonSerializer = $jsonSerializer;
        $this->tokenStorage = $tokenStorage;
    }

    private function getAuthUrl()
    {
        return sprintf('%s%s', $this->endpoint, $this->api->getPath());
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

        return $this->getNewToken();
    }

    private function getNewToken()
    {
        $requestContent = $this
            ->jsonSerializer
            ->serialize(array(
                '_username' => $this->username,
                '_password' => $this->password,
            ))
        ;

        $request = new Request(
            array(), // query
            array(), // request
            array(), // attributes
            array(), //cookies
            array(), // files
            array(), // server
            $requestContent
        );

        $request->setMethod(Request::METHOD_POST);

        $response = $this
            ->client
            ->send($this->getAuthUrl(), $request)
        ;

        switch ($response->getStatusCode()) {
            case 200:
                $content = $response->getContent();
                break;
            default:
                throw new AuthenticationException(sprintf('Authentication error: %s', $response->getContent()));
        }

        $contentArray = $this
            ->jsonSerializer
            ->unserialize($content)
        ;

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

    public function increaseUsageCounter()
    {
        $count = $this->getUsageCounter();

        $this
            ->tokenStorage
            ->write('usages', ++$count)
        ;

        return $count;
    }

    public function getUsageCounter()
    {
        return $this
            ->tokenStorage
            ->read('usages', 0)
        ;
    }

    public function destroy()
    {
        $this
            ->tokenStorage
            ->destroy()
        ;
    }

    public function updateRequest(Request $request, $forceNewToken = false)
    {
        if ($forceNewToken) {
            $token = $this->getNewToken();
        } else {
            $token = $this->getToken();
        }

        $request
            ->headers
            ->set(self::HTTP_HEADER_TOKEN, $token)
        ;
    }
}
