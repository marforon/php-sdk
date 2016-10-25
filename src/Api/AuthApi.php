<?php

namespace Locardi\PhpSdk\Api;

use Symfony\Component\HttpFoundation\Request;

class AuthApi implements ApiInterface
{
    public function getMethod()
    {
        return Request::METHOD_POST;
    }

    public function getPath()
    {
        return '/api/login';
    }
}
