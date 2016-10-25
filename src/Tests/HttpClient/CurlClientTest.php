<?php

namespace Locardi\PhpSdk\Tests\HttpClient;

use Locardi\PhpSdk\HttpClient\CurlClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CurlClientTest extends TestCase
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

        $request->setMethod(Request::METHOD_GET);

        $client = new CurlClient();
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
