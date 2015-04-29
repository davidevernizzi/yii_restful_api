<?php

class SafeApiController extends BaseApiController
{

    protected function getHeaders()
    {
        $headers =  apache_request_headers();

        return $headers;
    }

    protected function isAuthorised($headers)
    {
        return BaseApiController::OK;
    }
}
