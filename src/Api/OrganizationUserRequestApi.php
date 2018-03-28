<?php

namespace Locardi\PhpSdk\Api;

class OrganizationUserRequestApi implements ApiInterface
{
    const REQUEST_TYPE_HTML_PAGE = 'html_page';
    const REQUEST_TYPE_LOGIN = 'login';
    const REQUEST_TYPE_LOGIN_FAILED = 'login_failed';
    const REQUEST_TYPE_LOGIN_FAILED_WRONG_USERNAME = 'login_failed_wrong_username';
    const REQUEST_TYPE_LOGIN_FAILED_WRONG_PASSWORD = 'login_failed_wrong_password';
    const REQUEST_TYPE_DOWNLOAD = 'download';

    const USER_TYPE_INTERNAL = 'internal';
    const USER_TYPE_EXTERNAL = 'external';

    public function getMethod()
    {
        return 'POST';
    }

    public function getPath()
    {
        return '/collector/request';
    }
}
