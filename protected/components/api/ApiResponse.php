<?php

class ApiResponse extends CComponent
{
    public static function error($errorCode, $errorMessage='')
    {
        http_response_code($errorCode);
        return CJSON::encode(array(
            'code' => $errorCode,
            'error' => $errorMessage,
        ));
    }
}

?>
