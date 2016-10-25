<?php

namespace Locardi\PhpSdk\Tests\HttpClient;

use Locardi\PhpSdk\HttpClient\SocketClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class SocketClientTest extends TestCase
{
    public function testSend()
    {
        $data = array(
            'value1' => 'one',
            'value2' => 2,
        );
        $content = json_encode($data);

        $request = new Request(
            array(), // query
            array(), // request
            array(), // attributes
            array(), //cookies
            array(), // files
            array(), // server
            $content
        );

        $request->setMethod(Request::METHOD_POST);

        $request
            ->headers
            ->set('Content-Type', 'application/json')
        ;

        $request
            ->headers
            ->set('Content-Length', strlen($content))
        ;

        $client = new SocketClient();
        $response = $client->send(
            'https://www.google.co.uk/search?q=hello',
            $request
        );

        $this->assertEquals(200, $response->getStatusCode());

        // not working
        $response = $client->send(
            'https://www.domain-that-doesnt-exist.co.uk/?q=hello',
            $request
        );

        $this->assertEquals(503, $response->getStatusCode());
    }
}
