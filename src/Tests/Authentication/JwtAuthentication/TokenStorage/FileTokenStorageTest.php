<?php

namespace Locardi\PhpSdk\Tests\Authentication\JwtAuthentication\TokenStorage;

use Locardi\PhpSdk\Authentication\JwtAuthentication\TokenStorage\FileTokenStorage;
use PHPUnit\Framework\TestCase;

class FileTokenStorageTest extends TestCase
{
    public function testWrite()
    {
        $storage = new FileTokenStorage(__DIR__);

        $storage->write('one', 1);
        $storage->write('two', 2);

        $this->assertFileExists($storage->getFilePath());

        $expected = json_encode(array(
            'one' => 1,
            'two' => 2,
        ));

        $this->assertEquals($expected, file_get_contents($storage->getFilePath()));

        $storage->destroy();
    }

    public function testRead()
    {
        $storage = new FileTokenStorage(__DIR__);

        $storage->write('one', 1);
        $storage->write('two', 2);

        $this->assertEquals(1, $storage->read('one'));
        $this->assertEquals(2, $storage->read('two'));
        $this->assertEquals(3, $storage->read('hello', 3));

        $storage->destroy();
    }

    public function testDelete()
    {
        $storage = new FileTokenStorage(__DIR__);

        $storage->write('one', 1);
        $storage->write('two', 2);
        $storage->write('three', 3);

        $storage->delete('two');

        $this->assertEquals(1, $storage->read('one'));
        $this->assertEquals(-1, $storage->read('two', -1));
        $this->assertEquals(3, $storage->read('hello', 3));

        $storage->destroy();
    }
}
