<?php

namespace Locardi\PhpSdk\Authentication;

use Locardi\PhpSdk\Api\AuthApi;
use Locardi\PhpSdk\Exception\AuthenticationException;
use Locardi\PhpSdk\Authentication\JwtAuthentication\TokenStorage\TokenStorageInterface;
use Locardi\PhpSdk\HttpClient\CurlClient;
use Locardi\PhpSdk\Serializer\JsonSerializer;
use Zend\Diactoros\Request;

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

        $request = new Request(null, 'POST');

        $request
            ->getBody()
            ->write($requestContent)
        ;

        $response = $this
            ->client
            ->send($this->getAuthUrl(), $request)
        ;

        switch ($response->getStatusCode()) {
            case 200:
                $content = $response->getBody();
                break;
            default:
                throw new AuthenticationException(sprintf('Authentication error: %s', $response->getBody()));
        }

        $contentArray = $this
            ->jsonSerializer
            ->unserialize($content)
        ;

        if (!isset($contentArray['success'])) {
            throw new AuthenticationException(sprintf('Auth response format broken. %s', $response->getBody()));
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

    /**
     * @param bool $forceNewToken
     * @return string[]
     */
    public function getAuthHeaders($forceNewToken = false)
    {
        if ($forceNewToken) {
            $token = $this->getNewToken();
        } else {
            $token = $this->getToken();
        }

        return [
            self::HTTP_HEADER_TOKEN => $token,
        ];
    }
}
