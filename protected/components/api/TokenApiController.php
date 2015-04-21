<?php

class TokenApiController extends BaseApiController
{

    protected function getHeaders()
    {
        $headers =  apache_request_headers();
        if (!isset($headers['Authorization'])) {
            return null;
        }

        return $headers;
    }

    protected function isAuthorised($headers)
    {
        $auth = array();
        preg_match('/^token ([^:]*):([^:]*)$/', $headers['Authorization'], $auth);
        $resource = $_SERVER['REQUEST_METHOD'] . Yii::app()->controller->id;
        if (!is_array($auth) || count($auth) != 3) {
            return BaseApiController::BAD_AUTH_HEADER;
        }
        $restToken = RestTokens::model()->findByAttributes(array('client_id'=>$auth[1])); // TODO: check if api_key is still active
        if ($restToken == null) {
            return BaseApiController::BAD_TOKEN;
        }

        return BaseApiController::OK;
    }
}
