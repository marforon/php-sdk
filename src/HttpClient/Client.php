<?php

namespace Locardi\PhpSdk\HttpClient;

use Locardi\PhpSdk\Exception\HttpClientException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Client
{
    public function send($url, Request $request)
    {
        switch ($request->getMethod()) {
            case Request::METHOD_POST:
                return $this->post($url, $request);
                break;
            default:
                throw new HttpClientException(sprintf('Method %s not allowed.', $request->getMethod()));
        }
    }

    private function extractHeaders(Request $request)
    {
        $headers = array();
        foreach ($request->headers as $name => $value) {
            $headers[] = sprintf('%s: %s', $name, $value[0]);
        }

        return $headers;
    }

    public function post($url, Request $request)
    {
        $ch = curl_init();

        if ($request->getQueryString()) {
            $url = sprintf('%s?%s', $url, $request->getQueryString());
        }

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $this->extractHeaders($request),
            CURLOPT_POSTFIELDS => $request->getContent(),
        ));

        $result = curl_exec($ch);

        if ($result === false) {
            throw new HttpClientException(sprintf('Error contacting URL %s. %s', $url, curl_error($ch)));
        }

        $info = curl_getinfo($ch);

        $response = new Response($result, $info['http_code'], array(
            'Content-Type' => $info['content_type'],
        ));

        curl_close($ch);

        return $response;
    }
}
