<?php

namespace Locardi\PhpSdk\Authentication\JwtAuthentication\TokenStorage;

interface TokenStorageInterface
{
    public function write($key, $value);

    public function read($key, $default = null);

    public function delete($key);

    public function destroy();
}