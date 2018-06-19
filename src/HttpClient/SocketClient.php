<?php

namespace Locardi\PhpSdk\HttpClient;

use Zend\Diactoros\Request;
use Zend\Diactoros\Response;

class SocketClient implements HttpClientInterface
{
    public function getPathInfo($url, Request $request)
    {
        $parts = parse_url($url);

        $path = isset($parts['path']) ? $parts['path'] : '';

        $queryString = $request
            ->getUri()
            ->getQuery()
        ;
        if ($queryString) {
            $path .= '?' . $queryString;
        }

        $host = $parts['host'];
        $port = isset($parts['port']) ? $parts['port'] : 80;
        if ($parts['scheme'] == 'https') {
            $host = sprintf('ssl://%s', $host);
            $port = 443;
        }

        return [$request->getMethod(), $host, $port, $path];
    }

    public function send($url, Request $request)
    {
        list($method, $host, $port, $path) = $this->getPathInfo($url, $request);

        $timeout = 1;

        try {
            $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
        } catch (\Exception $e) {
            return new Response(
                $e->getMessage(),
                503
            );
        }

        if (!$fp) {
            return new Response(
                sprintf('Error contacting URL %s. %s', $url, $errstr),
                503 // service unavailable
            );
        }

        $out = array();
        $out[] = sprintf("%s %s HTTP/1.1", $method, $path);
        $out[] = sprintf("Host: %s:%s", $host, $port);
        foreach ($this->extractHeaders($request) as $header) {
            $out[] = sprintf("%s", $header);
        }
        $out[] = "Connection: Close\r\n";
        if ($request->getBody()) {
            $out[] = $request->getBody();
        }

        $out = implode("\r\n", $out);

        fwrite($fp, $out);
        usleep(500);
        fclose($fp);

        return new Response();
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
