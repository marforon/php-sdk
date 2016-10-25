<?php

namespace Locardi\PhpSdk;

use Locardi\PhpSdk\Api\AuthApi;
use Locardi\PhpSdk\Api\OrganizationUserRequestApi;
use Locardi\PhpSdk\Authentication\JwtAuthentication;
use Locardi\PhpSdk\Exception\ClientException;
use Locardi\PhpSdk\HttpClient\CurlClient;
use Locardi\PhpSdk\HttpClient\SocketClient;
use Locardi\PhpSdk\Serializer\JsonSerializer;

class LocardiClient
{
    const DEFAULT_ENDPOINT = '';

    private $client;

    /**
     * LocardiClient constructor.
     *
     * Config
     * - debug [optional] default is false
     * - username [required]
     * - password [required]
     * - tokenStorage [required] A TokenStorageInterface object for saving the JWT token
     * - endpoint [optional] It overrides the default one (useful for staging)
     * - authEndpoint [optional] It overrides the default one (useful for staging)
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        // required params
        $debug = $this->getRequiredParam($config, 'debug');
        $username = $this->getRequiredParam($config, 'username');
        $password = $this->getRequiredParam($config, 'password');
        $tokenStorage = $this->getRequiredParam($config, 'tokenStorage');

        // optional params
        $endpoint = $this->getParam($config, 'endpoint', self::DEFAULT_ENDPOINT);

        $jsonSerializer = new JsonSerializer();
        $curlClient = new CurlClient();

        $authentication = new JwtAuthentication(
            $curlClient,
            $endpoint,
            new AuthApi(),
            $username,
            $password,
            $jsonSerializer,
            $tokenStorage
        );

        $this->client = new Client(
            $endpoint,
            $authentication,
            $debug ? $curlClient : new SocketClient(),
            $jsonSerializer
        );
    }

    private function getParam(array $params, $key, $default = null)
    {
        if (isset($params[$key])) {
            return $params[$key];
        }

        return $default;
    }

    private function getRequiredParam(array $params, $key)
    {
        if (isset($params[$key])) {
            return $params[$key];
        }

        throw new ClientException(sprintf('Required param %s is missing.', $key));
    }

    public function send(array $data)
    {
        if (isset($data['organization_user_request'])) {
            $this
                ->client
                ->send(new OrganizationUserRequestApi(), $data)
            ;
        } else {
            throw new ClientException('Invalid API');
        }
    }
}