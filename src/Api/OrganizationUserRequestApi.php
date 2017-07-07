<?php

namespace Locardi\PhpSdk\Api;

use Symfony\Component\HttpFoundation\Request;

class OrganizationUserRequestApi implements ApiInterface
{
    const REQUEST_TYPE_HTML_PAGE = 'html_page';
    const REQUEST_TYPE_LOGIN = 'login';
    const REQUEST_TYPE_LOGIN_FAILED = 'login_failed';
    const REQUEST_TYPE_DOWNLOAD = 'download';

    const USER_TYPE_INTERNAL = 'internal';
    const USER_TYPE_EXTERNAL = 'external';

    public function getMethod()
    {
        return Request::METHOD_POST;
    }

    public function getPath()
    {
        return '/collector/request';
    }
}
