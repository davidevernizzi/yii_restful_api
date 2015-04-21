<?php

class HmacApiController extends BaseApiController
{

    protected function getHeaders()
    {
        $headers =  apache_request_headers();
        if (!isset($headers['Authorization'])) {
            return null;
        }
        if (!isset($headers['Timestamp'])) {
            return null;
        }

        return $headers;
    }

    private function getTimestamp($headers)
    {
        return $headers['Timestamp'];
    }

    private function isOnTime($headers)
    {
        $timestamp = $this->getTimestamp($headers);
        
        $allowedWindow = 3600; // 1 hour. TODO: get this from config
        if(abs($timestamp - time()) > $allowedWindow) {
            return false;
        }

        return true;
    }

    protected function isAuthorised($headers)
    {
        if(!$this->isOnTime($headers)) {
            return BaseApiController::TIMEOUT;
        }

        $timestamp = $this->getTimestamp($headers);
        $auth = array();
        preg_match('/^hmac ([^:]*):([^:]*)$/', $headers['Authorization'], $auth);
        $resource = $_SERVER['REQUEST_METHOD'] . Yii::app()->controller->id;
        if (!is_array($auth) || count($auth) != 3) {
            return BaseApiController::BAD_AUTH_HEADER;
        }
        $restToken = RestTokens::model()->findByAttributes(array('client_id'=>$auth[1])); // TODO: check if api_key is still active
        if ($restToken == null) {
            return BaseApiController::BAD_TOKEN;
        }
        $secret = $restToken->client_secret;
        $hmac = $auth[2];
        if(!Hmac::verify($hmac, $timestamp, $secret, $this->data, $resource)) {
            return BaseApiController::BAD_HMAC;
        }

        return BaseApiController::OK;
    }
}
