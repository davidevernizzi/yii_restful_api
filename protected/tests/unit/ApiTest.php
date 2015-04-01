<?php

class ApiCall {
    private static $apiUrl = 'http://192.168.56.101/yii_restful_api/index-test.php/';

    public static function get($resource, $payload='')
    {
        $cmd = "curl '$apiUrl$resource/$payload'";
        $output = shell_exec($cmd);
        return $output;
    }

    private static function call($verb, $resource, $payload)
    {
        $apiUrl = self::$apiUrl;
        $cmd = "curl -H 'Content-Type: application/json' -d '" . CJSON::encode($payload) . "' -X $verb '$apiUrl$resource'";
        $output = shell_exec($cmd); //TODO: remove stderr
        return $output;
    }

    public static function post($resource, $payload='')
    {
        return self::call('POST', $resource, $payload);
    }

    public static function put($resource, $payload='')
    {
        return self::call('PUT', $resource, $payload);
    }

    public static function delete($resource, $payload='')
    {
        return self::call('DELETE', $resource, $payload);
    }
}


class ApiTest extends CDbTestCase  {
    public $fixtures=array(
                'tokens' => 'RestTokens',
            );

    public function setUp() 
    {
        parent::setUp();
    }

    public function testMissingActions()
    {
        $expected_json = '{"code":"501","error":"Not Implemented"}';

        $output = ApiCall::post('foo');

        $this->assertEquals($output, $expected_json);
    }

    public function testAuthenticationHmacCreation_OK()
    {
        $hmac = Hmac::create('12345', 'XXX', array('FOO' => '2', 'bar' => 1));
        $this->assertEquals($hmac, '27ce2f86a19e57e4167a30f5e7bbc29fec869900');

        $hmac = Hmac::verify('8d882e0f2a73fbbbe6884cebae866871382fa364', '12345', 'XXX', array('FOO' => '2', 'bar' => 1), 'POST');
        $this->assertTrue($hmac);
    }

    public function testAuthenticationDigestCreation_KO()
    {
        $hmac = Hmac::verify('7d882e0f2a73fbbbe6884cebae866871382fa364', '12345', 'XXX', array('FOO' => '2', 'bar' => 1), 'POST');
        $this->assertFalse($hmac);

        $hmac = Hmac::verify('8d882e0f2a73fbbbe6884cebae866871382fa364', '12345', 'yyy', array('FOO' => '2', 'bar' => 1), 'POST');
        $this->assertFalse($hmac);
    }

    public function testAuthenticationAPI_OK()
    {

    }

}
