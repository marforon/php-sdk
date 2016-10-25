<?php

namespace Locardi\PhpSdk\Authentication\JwtAuthentication\TokenStorage;

use Locardi\PhpSdk\Exception\AuthenticationException;

class FileTokenStorage implements TokenStorageInterface
{
    const FILE_NAME = 'locardi-php-sdk.json';

    private $dir;

    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    private function init()
    {
        if (!is_dir($this->dir)) {
            throw new AuthenticationException(sprintf('Directory %s not found.', $this->dir));
        }

        if (!is_writable($this->dir)) {
            throw new AuthenticationException(sprintf('Directory %s is not writable.', $this->dir));
        }

        if (!file_exists($this->getFilePath())) {
            $this->writeFile([]);
        }
    }

    public function getFilePath()
    {
        return sprintf('%s/%s', $this->dir, self::FILE_NAME);
    }

    public function write($key, $value)
    {
        $this->init();

        $data = $this->readAll();

        $data[$key] = $value;

        $this->writeFile($data);
    }

    private function readAll()
    {
        return json_decode(file_get_contents($this->getFilePath()), true);
    }

    private function writeFile(array $data)
    {
        file_put_contents($this->getFilePath(), json_encode($data));
    }

    public function read($key, $default = null)
    {
        $this->init();

        $data = $this->readAll();

        return isset($data[$key]) ? $data[$key] : $default;
    }

    public function delete($key)
    {
        $this->init();

        $data = $this->readAll();

        if (isset($data[$key])) {
            unset($data[$key]);
        }

        $this->writeFile($data);
    }

    public function destroy()
    {
        unlink($this->getFilePath());
    }
}
