<?php

namespace Locardi\PhpSdk\Api;

use Symfony\Component\HttpFoundation\Request;

class OrganizationUserRequestApi implements ApiInterface
{
    public function getMethod()
    {
        return Request::METHOD_POST;
    }

    public function getPath()
    {
        return '/collector/request';
    }
}
