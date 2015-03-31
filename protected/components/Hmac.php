<?php

class Hmac extends CComponent
{
    public $textWidth;

    public static function create($timestamp, $secret, $params, $verb='')
    {
        ksort($params);
        $str = $secret . $verb;

        foreach($params as $key=>$value) {
            $str .= strtolower($key) . '=' . $value;
        }

        $str .= $timestamp;

        return sha1($str);
    }

    public static function verify($hmac, $timestamp, $secret, $params, $verb='')
    {
        $computed_hmac = self::create($timestamp, $secret, $params, $verb);
        if ($computed_hmac === $hmac) {
            return true;
        }
        else {
            return false;
        }
    }
}

?>
