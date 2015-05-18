<?php

require_once('Util.php');

class ApiCall2 {
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


class CrudApiTest extends CDbTestCase  {
    public $fixtures=array(
                'cruds' => 'CrudTest',
            );

    public function setUp() 
    {
        parent::setUp();
    }

    public function testGet()
    {
        $params = '';

        $crud = array();
        $crud_models = CrudTest::model()->findAll();
        foreach($crud_models as $model) {
            $crud[] = $model->attributes;
        }
        $expected_res = json_encode($crud);

        $output = ApiCall::get('crud', $params, array());
        $this->assertEquals($output, $expected_res);
    }

    public function testPostOK()
    {
        $params = '{"key1":"value5_1","key2":"value5_2"}';
        $expected_res = '5';

        $output = ApiCall::post('crud', $params, array());
        $this->assertEquals($output, $expected_res);

        $model = CrudTest::model()->findByPk($output);
        $this->assertNotNull($model);
        $this->assertEquals('value5_1', $model->key1);
        $this->assertEquals('value5_2', $model->key2);
    }

}
