<?php

namespace Locardi\PhpSdk\Api;

class AuthApi implements ApiInterface
{
    public function getMethod()
    {
        return 'POST';
    }

    public function getPath()
    {
        return '/collector/login';
    }
}
