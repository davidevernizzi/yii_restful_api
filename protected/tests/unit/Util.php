<?php

class ApiCall {
    private static $apiUrl = 'http://192.168.56.101/yii_restful_api/index-test.php/';

    public static function get($resource, $payload='', $header='')
    {
        $headerStr = "-H 'Content-Type: application/json'";
        if (is_array($header)) {
            foreach($header as $key => $value) {
                $headerStr .= " -H '$key: $value'";
            }
        }
        $apiUrl = self::$apiUrl;
        $cmd = "curl $headerStr '$apiUrl$resource/$payload' 2> /dev/null";
        $output = shell_exec($cmd);
        return $output;
    }

    private static function call($verb, $resource, $payload, $header)
    {
        $headerStr = "-H 'Content-Type: application/json'";
        if (is_array($header)) {
            foreach($header as $key => $value) {
                $headerStr .= " -H '$key: $value'";
            }
        }
        $apiUrl = self::$apiUrl;
        $cmd = "curl $headerStr -d '" . CJSON::encode($payload) . "' -X $verb '$apiUrl$resource' 2> /dev/null";
        $output = shell_exec($cmd);
        return $output;
    }

    public static function post($resource, $payload='', $header='')
    {
        return self::call('POST', $resource, $payload, $header);
    }

    public static function put($resource, $payload='', $header='')
    {
        return self::call('PUT', $resource, $payload, $header);
    }

    public static function delete($resource, $payload='', $header='')
    {
        return self::call('DELETE', $resource, $payload, $header);
    }
}

