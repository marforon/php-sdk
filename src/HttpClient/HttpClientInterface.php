<?php

namespace Locardi\PhpSdk\HttpClient;

use Zend\Diactoros\Request;
use Zend\Diactoros\Response;

interface HttpClientInterface
{
    /**
     * @param $url
     * @param Request $request
     * @return Response
     */
    public function send($url, Request $request);
}