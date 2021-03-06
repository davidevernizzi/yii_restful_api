<?php

require_once('Util.php');

class ApiCall3 {
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


class ApiTest extends CDbTestCase  {
    public $fixtures=array(
                'tokens' => 'RestTokens',
            );

    public function setUp() 
    {
        parent::setUp();
    }

    public function testAuthenticationHmacCreation_OK()
    {
        $hmac = Hmac::create('12345', 'XXX', array('FOO' => '2', 'bar' => 1));
        $this->assertEquals($hmac, '27ce2f86a19e57e4167a30f5e7bbc29fec869900');

        $hmac = Hmac::verify('8d882e0f2a73fbbbe6884cebae866871382fa364', '12345', 'XXX', array('FOO' => '2', 'bar' => 1), 'POST');
        $this->assertTrue($hmac);

        $hmac = Hmac::create('12345', 'xxx', '', 'POSTfoo');
        $this->assertEquals($hmac, '84557e627da7390858a693c84eecb0e4ff56ba77');

        $hmac = Hmac::create('12345', 'xxx', array('key1'=>'value1', 'key2'=>'value2'), 'POSTfoo');
        $this->assertEquals($hmac, '3529940e5dd1e257d7a79d0bf6d132e5899aa208');
    }

    public function testAuthenticationDigestCreation_KO()
    {
        $hmac = Hmac::verify('7d882e0f2a73fbbbe6884cebae866871382fa364', '12345', 'XXX', array('FOO' => '2', 'bar' => 1), 'POST');
        $this->assertFalse($hmac);

        $hmac = Hmac::verify('8d882e0f2a73fbbbe6884cebae866871382fa364', '12345', 'yyy', array('FOO' => '2', 'bar' => 1), 'POST');
        $this->assertFalse($hmac);
    }

    public function testAuthenticationAPI_KO_WrongHeaders()
    {
        $expected_json = '{"code":"400","error":"Bad Request"}';
        $timestamp = time();

        $output = ApiCall::post('foo');
        $this->assertEquals($output, $expected_json);

        $output = ApiCall::post('foo', '', array('Authorization' => 'hmac johndoe:FAKEHMAC'));
        $this->assertEquals($output, $expected_json);

        $output = ApiCall::post('foo', '', array('Timestamp' => $timestamp));
        $this->assertEquals($output, $expected_json);

        $output = ApiCall::post('foo', '', array('Authorization' => 'WRONG:HEADER', 'Timestamp' => $timestamp));
        $this->assertEquals($output, $expected_json);
    }

    public function testAuthenticationAPI_KO_FakeToken()
    {
        $expected_json = '{"code":"401","error":"Unauthorized"}';
        $timestamp = time();

        $output = ApiCall::post('foo', '', array('Authorization' => 'hmac FAKEJOHN:FAKEHMAC', 'Timestamp' => $timestamp));

        $this->assertEquals($output, $expected_json);
    }
    public function testAuthenticationAPI_KO_FakeHmac()
    {
        $expected_json = '{"code":"401","error":"Unauthorized"}';
        $timestamp = time();

        $output = ApiCall::post('foo', '', array('Authorization' => 'hmac johndoe:FAKEHMAC', 'Timestamp' => $timestamp));

        $this->assertEquals($output, $expected_json);
    }

    public function testAuthenticationAPI_KO_WrongTimestamp()
    {
        $expected_json = '{"code":"401","error":"Unauthorized"}';

        $output = ApiCall::post('foo', '', array('Authorization' => 'hmac johndoe:84557e627da7390858a693c84eecb0e4ff56ba77', 'Timestamp' => '12345'));

        $this->assertEquals($output, $expected_json);
    }

    public function testMissingActions()
    {
        $expected_json = '{"code":"501","error":"Not Implemented"}';

        $token = $this->tokens('token1');

        $timestamp = time();
        $params = '';
        $hmac = Hmac::create($timestamp, $token->client_secret, $params, 'POSTfoo');
        $auth = "hmac " . $token->client_id . ":$hmac";

        $output = ApiCall::post('foo', $params, array('Authorization' => $auth, 'Timestamp' => $timestamp));

        $this->assertEquals($output, $expected_json);
        
        $params = array('key1'=>'value1', 'key2'=>'value2');
        $hmac = Hmac::create($timestamp, $token->client_secret, $params, 'POSTfoo');
        $auth = "hmac " . $token->client_id . ":$hmac";

        $output = ApiCall::post('foo', $params, array('Authorization' => $auth, 'Timestamp' => $timestamp));

        $this->assertEquals($output, $expected_json);

        $token = $this->tokens('token3');

        $params = array('key1'=>'value1', 'key2'=>'value2');
        $hmac = Hmac::create($timestamp, $token->client_secret, $params, 'POSTfoo');
        $auth = "hmac " . $token->client_id . ":$hmac";

        $output = ApiCall::post('foo', $params, array('Authorization' => $auth, 'Timestamp' => $timestamp));

        $this->assertEquals($output, $expected_json);
    }

    public function testMockups()
    {
        $token = $this->tokens('token1');

        $timestamp = time();
        $params = '';

        $hmac = Hmac::create($timestamp, $token->client_secret, $params, 'GETbar');
        $auth = "hmac " . $token->client_id . ":$hmac";
        $expected_res = "BAR INDEX\n";
        $output = ApiCall::get('bar', $params, array('Authorization' => $auth, 'Timestamp' => $timestamp));
        $this->assertEquals($output, $expected_res);

        $hmac = Hmac::create($timestamp, $token->client_secret, $params, 'POSTbar');
        $auth = "hmac " . $token->client_id . ":$hmac";
        $expected_res = '{"code":"501","error":"Not Implemented"}';
        $output = ApiCall::post('bar', $params, array('Authorization' => $auth, 'Timestamp' => $timestamp));
        $this->assertEquals($output, $expected_res);

        $hmac = Hmac::create($timestamp, $token->client_secret, $params, 'PUTbar');
        $auth = "hmac " . $token->client_id . ":$hmac";
        $expected_res = "BAR UPDATE\n";
        $output = ApiCall::put('bar', $params, array('Authorization' => $auth, 'Timestamp' => $timestamp));
        $this->assertEquals($output, $expected_res);
    }

    public function testApiToken()
    {
        $token = $this->tokens('token1');

        $timestamp = time();
        $params = '';

        $auth = "token " . $token->client_id . ":" . $token->client_secret;
        $expected_res = "GET token";
        $output = ApiCall::get('token', $params, array('Authorization' => $auth));
        $this->assertEquals($output, $expected_res);

        $expected_res = '{"code":"501","error":"Not Implemented"}';
        $output = ApiCall::post('token', $params, array('Authorization' => $auth));
        $this->assertEquals($output, $expected_res);
    }

    public function testSafeApi()
    {
        $params = '';

        $expected_res = "GET safe";
        $output = ApiCall::get('safe', $params, array());
        $this->assertEquals($output, $expected_res);

        $expected_res = '{"code":"501","error":"Not Implemented"}';
        $output = ApiCall::post('safe', $params, array());
        $this->assertEquals($output, $expected_res);
    }

}
