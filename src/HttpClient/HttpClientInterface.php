<?php

namespace Locardi\PhpSdk\HttpClient;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface HttpClientInterface
{
    /**
     * @param $url
     * @param Request $request
     * @return Response
     */
    public function send($url, Request $request);
}