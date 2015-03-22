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
    /*
    public $fixtures=array(
                'tokens' => 'RestTokens',
            );
     */

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

}
