<?php

namespace Locardi\PhpSdk;

use Locardi\PhpSdk\Client;
use Locardi\PhpSdk\HttpClient\Client as HttpClient;

class LocardiClient
{
    const DEFAULT_ENDPOINT = '';

    const DEFAULT_AUTH_ENDPOINT = '';

    private $client;

    /**
     * LocardiClient constructor.
     *
     * Config
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
        $this->validateConfigFields($config);

        $username = $config['username'];
        $password = $config['password'];
        $tokenStorage = $config['tokenStorage'];
        $endpoint = isset($config['endpoint']) ? $config['endpoint'] : self::DEFAULT_ENDPOINT;
        $authEndpoint = isset($config['authEndpoint']) ? $config['authEndpoint'] : self::DEFAULT_ENDPOINT;

        $httpClient = new HttpClient();

        $authentication = new \Locardi\PhpSdk\Authentication\JwtAuthentication(
            $httpClient,
            $authEndpoint,
            $username,
            $password,
            $tokenStorage
        );

        $serializer = new \Locardi\PhpSdk\Serializer\JsonSerializer();

        $this->client = new Client(
            $endpoint,
            $authentication,
            $httpClient,
            $serializer
        );
    }

    private function getRequiredConfigFields()
    {
        return array(
            'username',
            'password',
            'tokenStorage',
        );
    }

    private function validateConfigFields(array $config)
    {
        foreach ($this->getRequiredConfigFields() as $requiredConfigField) {
            if (!isset($config[$requiredConfigField])) {
                throw new \Exception(sprintf('Config field %s is required.', $requiredConfigField));
            }
        }
    }

    public function send(array $data)
    {
        $this
            ->client
            ->send($data)
        ;
    }
}