<?php

namespace Locardi\PhpSdk\HttpClient;

use Zend\Diactoros\Request;
use Zend\Diactoros\Response;

class CurlClient implements HttpClientInterface
{
    public function send($url, Request $request)
    {
        $ch = curl_init();

        $queryString = $request
            ->getUri()
            ->getQuery()
        ;

        if ($queryString) {
            $url = sprintf('%s?%s', $url, $queryString);
        }

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $this->extractHeaders($request),
        ));

        switch (strtolower($request->getMethod())) {
            case 'get':
                break;
            case 'post':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, (string) $request->getBody());
                break;
            default:
                throw new \Exception(sprintf('Method %s not supported.', $request->getMethod()));
        }

        $result = curl_exec($ch);

        if ($result === false) {
            throw new \Exception(sprintf('Error contacting URL %s. %s', $url, curl_error($ch)));
        }

        $info = curl_getinfo($ch);

        $response = new Response('php://memory', $info['http_code'], [
            'Content-Type' => $info['content_type'],
        ]);

        $response
            ->getBody()
            ->write($result)
        ;

        curl_close($ch);

        return $response;
    }

    private function extractHeaders(Request $request)
    {
        $headers = array();
        foreach ($request->getHeaders() as $name => $value) {
            $headers[] = sprintf('%s: %s', $name, $value[0]);
        }

        return $headers;
    }
}