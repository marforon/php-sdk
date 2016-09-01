<?php

namespace Locardi\PhpSdk\Serializer;

interface SerializerInterface
{
    public function serialize(array $data);

    public function unserialize($payload);

    public function getHttpHeaderContentType();
}