<?php

namespace Locardi\PhpSdk\Serializer;

class JsonSerializer implements SerializerInterface
{
    public function serialize(array $data)
    {
        return json_encode($data);
    }

    public function unserialize($payload)
    {
        return json_decode($payload, true);
    }

    public function getHttpHeaderContentType()
    {
        return 'application/json';
    }
}
