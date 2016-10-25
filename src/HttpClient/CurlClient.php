<?php

namespace Locardi\PhpSdk\HttpClient;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CurlClient implements HttpClientInterface
{
    public function send($url, Request $request)
    {
        $ch = curl_init();

        if ($request->getQueryString()) {
            $url = sprintf('%s?%s', $url, $request->getQueryString());
        }

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $this->extractHeaders($request),
        ));

        switch ($request->getMethod()) {
            case Request::METHOD_GET:
                break;
            case Request::METHOD_POST:
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getContent());
                break;
            default:
                throw new \Exception(sprintf('Method %s not supported.', $request->getMethod()));
        }

        $result = curl_exec($ch);

        if ($result === false) {
            throw new \Exception(sprintf('Error contacting URL %s. %s', $url, curl_error($ch)));
        }

        $info = curl_getinfo($ch);

        $response = new Response($result, $info['http_code'], array(
            'Content-Type' => $info['content_type'],
        ));

        curl_close($ch);

        return $response;
    }

    private function extractHeaders(Request $request)
    {
        $headers = array();
        foreach ($request->headers as $name => $value) {
            $headers[] = sprintf('%s: %s', $name, $value[0]);
        }

        return $headers;
    }
}