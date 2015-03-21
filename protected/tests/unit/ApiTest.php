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
        $expected_json = '{"code":"301","error":"Method not available"}';

        $output = ApiCall::put('foo');

        $this->assertEquals($output, $expected_json);
    }

    public function _testAuth() 
    {
        $hmac = 'e1a5cb35f944264b3db11ac6a6cbe9079a43d9e3';
        $token = $this->tokens('token1');
        $auth = $token->auth(
            array('time'=>'10','distance'=>'10','datetime'=>'01-03-2015','timestamp'=>'12345','hmac'=>$hmac), $hmac 
        );

        $this->assertTrue($auth);

        $hmac = '1c8c1a8e75c47409088204791807651939ec98f5';
        $token = $this->tokens('token1');
        $auth = $token->auth(
            array('client_id'=>'FOO','timestamp'=>'1234','hmac'=>$hmac), $hmac 
        );

        $this->assertTrue($auth);
    }

    public function _testAuthFail()
    {
        $hmac = 'BOGUS';
        $token = $this->tokens('token1');
        $auth = $token->auth(
            array('time'=>'10','distance'=>'10','datetime'=>'01-03-2015','timestamp'=>'12345','hmac'=>$hmac), $hmac 
        );

        $this->assertFalse($auth);
    }

    public function _testAuthFailOldTs() //TODO
    {
        $hmac = 'BOGUS';
        $token = $this->tokens('token1');
        $auth = $token->auth(
            array('time'=>'10','distance'=>'10','datetime'=>'01-03-2015','timestamp'=>'12345','hmac'=>$hmac), $hmac 
        );

        $this->assertFalse($auth);
    }

    public function _testGetOK()
    {
        $expected_json_user1 = '[[{"id":"1","time":"11","distance":null,"speed":null,"datetime":"Thu 1st Jan 15"},{"id":"2","time":"12","distance":null,"speed":null,"datetime":"Thu 1st Jan 15"},{"id":"3","time":"13","distance":null,"speed":null,"datetime":"Thu 1st Jan 15"},{"updated_at":"0000-00-00 00:00:00","id":-1,"time":36,"distance":0,"speed":0,"user_id":null,"datetime":null,"created_at":null}]]';
        $token = $this->tokens('token1');

        $cmd = "curl 'http://192.168.56.101/toptal_task/index-test.php/api?client_id=" . $token->client_id . "&timestamp=1234&hmac=1c8c1a8e75c47409088204791807651939ec98f5' 3> /dev/null";
        $output = shell_exec($cmd);

        $this->assertEquals($output, $expected_json_user1);
    }

    public function _testGetOKFilter()
    {
        $expected_json_user1 = '[[{"id":"17","time":"104","distance":"104","speed":null,"datetime":"Sun 8th Mar 15"},{"id":"16","time":"103","distance":"103","speed":null,"datetime":"Sat 7th Mar 15"},{"id":"15","time":"102","distance":"102","speed":null,"datetime":"Fri 6th Mar 15"},{"id":"14","time":"101","distance":"101","speed":null,"datetime":"Thu 5th Mar 15"},{"updated_at":"0000-00-00 00:00:00","id":-1,"time":410,"distance":410,"speed":60,"user_id":null,"datetime":null,"created_at":null}]]';
        $token = $this->tokens('token3');

        $cmd = "curl 'http://192.168.56.101/toptal_task/index-test.php/api?client_id=" . $token->client_id . "&from=1-03-2015&timestamp=1234&hmac=1b9da52ddf10bda75155a2778c653b99d676ddff' 3> /dev/null";
        $output = shell_exec($cmd);

        $this->assertEquals($output, $expected_json_user1);
    }

    public function _testGetWrongAuth()
    {
        $expected_json_user1 = '';
        $token = $this->tokens('token1');

        $cmd = "curl 'http://192.168.56.101/toptal_task/index-test.php/api?client_id=" . $token->client_id . "&timestamp=1234&hmac=BOGUS.1c8c1a8e75c47409088204791807651939ec98f5' 3> /dev/null";
        $output = shell_exec($cmd);

        $this->assertEquals($output, $expected_json_user1);
    }

    public function _testPostOK()
    {
        $expected_json_user1 = '[[{"id":"21","time":"50","distance":"50","speed":"60","datetime":"Sun 23rd Nov 14"},{"updated_at":"0000-00-00 00:00:00","id":-1,"time":50,"distance":50,"speed":60,"user_id":null,"datetime":null,"created_at":null}]]';
        $token = $this->tokens('token5');
        
        // POST a new time for user 1 (token1) [update $expected_json_user1]
        $params = array(
            'time' => '50',
            'distance' => '50',
            'datetime' => '23-11-2014',
            'client_id' => $token->client_id,
            'timestamp' => '1234',
            'hmac' => '0710b1325de611a60dc7f64f3c286f4fe1cd66f6',
        );

        $cmd = "curl -H 'Content-Type: application/json' -d '" . CJSON::encode($params) . "' -X POST 'http://192.168.56.101/toptal_task/index-test.php/api/'";
        $output = shell_exec($cmd);
        $this->assertEquals('{"success"}', $output);

        $cmd = "curl 'http://192.168.56.101/toptal_task/index-test.php/api?client_id=" . $token->client_id . "&timestamp=1234&hmac=ab789c387b8e5289e4621489cce913b4082a7f06' 3> /dev/null";
        $output = shell_exec($cmd);
        $this->assertEquals($expected_json_user1, $output);
    }

    public function _testPutOK()
    {
        $expected_json_user1 = '[[{"id":"2","time":"12","distance":null,"speed":null,"datetime":"Thu 1st Jan 15"},{"id":"3","time":"13","distance":null,"speed":null,"datetime":"Thu 1st Jan 15"},{"updated_at":"0000-00-00 00:00:00","id":-1,"time":25,"distance":0,"speed":0,"user_id":null,"datetime":null,"created_at":null}],[{"id":"1","time":"50","distance":"50","speed":"60","datetime":"Sun 23rd Nov 14"},{"updated_at":"0000-00-00 00:00:00","id":-1,"time":50,"distance":50,"speed":60,"user_id":null,"datetime":null,"created_at":null}]]';
        // modify a time for user 1 (token1) [update $expected_json_user1]
        
        $token = $this->tokens('token1');
        
        // POST a new time for user 1 (token1) [update $expected_json_user1]
        $params = array(
            'id' => '1',
            'time' => '50',
            'distance' => '50',
            'datetime' => '23-11-2014',
            'client_id' => $token->client_id,
            'timestamp' => '1234',
            'hmac' => '0f7d6343dc9479870803fe00a19a96f3e4aeabc6',
        );

        $cmd = "curl -H 'Content-Type: application/json' -d '" . CJSON::encode($params) . "' -X PUT 'http://192.168.56.101/toptal_task/index-test.php/api/'";
        $output = shell_exec($cmd);
        $this->assertEquals('{"success"}', $output);

        $cmd = "curl 'http://192.168.56.101/toptal_task/index-test.php/api?client_id=" . $token->client_id . "&timestamp=1234&hmac=1c8c1a8e75c47409088204791807651939ec98f5' 3> /dev/null";
        $output = shell_exec($cmd);
        $this->assertEquals($expected_json_user1, $output);
    }

    public function _testPutKOMissingId()
    {
        $expected_json_user1 = '[[{"id":"2","time":"12","distance":null,"speed":null,"datetime":"Thu 1st Jan 15"},{"id":"3","time":"13","distance":null,"speed":null,"datetime":"Thu 1st Jan 15"},{"updated_at":"0000-00-00 00:00:00","id":-1,"time":25,"distance":0,"speed":0,"user_id":null,"datetime":null,"created_at":null}],[{"id":"1","time":"50","distance":"50","speed":"60","datetime":"Sun 23rd Nov 14"},{"updated_at":"0000-00-00 00:00:00","id":-1,"time":50,"distance":50,"speed":60,"user_id":null,"datetime":null,"created_at":null}]]';
        // modify a time for user 1 (token1) [update $expected_json_user1]
        
        $token = $this->tokens('token1');
        
        // POST a new time for user 1 (token1) [update $expected_json_user1]
        $params = array(
            'id' => '99',
            'time' => '50',
            'distance' => '50',
            'datetime' => '23-11-2014',
            'client_id' => $token->client_id,
            'timestamp' => '1234',
            'hmac' => '59179bcfd592c037c748d03e82fc50a3b5a51d79',
        );

        $cmd = "curl -H 'Content-Type: application/json' -d '" . CJSON::encode($params) . "' -X PUT 'http://192.168.56.101/toptal_task/index-test.php/api/'";
        $output = shell_exec($cmd);
        $this->assertEquals('{"error":"Time not found"}', $output);

        $cmd = "curl 'http://192.168.56.101/toptal_task/index-test.php/api?client_id=" . $token->client_id . "&timestamp=1234&hmac=1c8c1a8e75c47409088204791807651939ec98f5' 3> /dev/null";
        $output = shell_exec($cmd);
        $this->assertEquals($expected_json_user1, $output);
    }

    public function _testPutKONotAuth()
    {
        $expected_json_user1 = '[[{"id":"2","time":"12","distance":null,"speed":null,"datetime":"Thu 1st Jan 15"},{"id":"3","time":"13","distance":null,"speed":null,"datetime":"Thu 1st Jan 15"},{"updated_at":"0000-00-00 00:00:00","id":-1,"time":25,"distance":0,"speed":0,"user_id":null,"datetime":null,"created_at":null}],[{"id":"1","time":"50","distance":"50","speed":"60","datetime":"Sun 23rd Nov 14"},{"updated_at":"0000-00-00 00:00:00","id":-1,"time":50,"distance":50,"speed":60,"user_id":null,"datetime":null,"created_at":null}]]';
        // modify a time for user 1 (token1) [update $expected_json_user1]
        
        $token = $this->tokens('token1');
        
        // POST a new time for user 1 (token1) [update $expected_json_user1]
        $params = array(
            'id' => '4',
            'time' => '50',
            'distance' => '50',
            'datetime' => '23-11-2014',
            'client_id' => $token->client_id,
            'timestamp' => '1234',
            'hmac' => 'd70eee075233b9ee9985afce5c3262bcc3087d2c',
        );

        $cmd = "curl -H 'Content-Type: application/json' -d '" . CJSON::encode($params) . "' -X PUT 'http://192.168.56.101/toptal_task/index-test.php/api/'";
        $output = shell_exec($cmd);
        $this->assertEquals('{"error":"Not authorised"}', $output);

        $cmd = "curl 'http://192.168.56.101/toptal_task/index-test.php/api?client_id=" . $token->client_id . "&timestamp=1234&hmac=1c8c1a8e75c47409088204791807651939ec98f5' 3> /dev/null";
        $output = shell_exec($cmd);
        $this->assertEquals($expected_json_user1, $output);
    }

    public function _testDeleteOK()
    {
        $expected_json_user1 = '[[{"id":"2","time":"12","distance":null,"speed":null,"datetime":"Thu 1st Jan 15"},{"id":"3","time":"13","distance":null,"speed":null,"datetime":"Thu 1st Jan 15"},{"updated_at":"0000-00-00 00:00:00","id":-1,"time":25,"distance":0,"speed":0,"user_id":null,"datetime":null,"created_at":null}]]';
        $token = $this->tokens('token1');
        
        $params = array(
            'id' => '1',
            'client_id' => $token->client_id,
            'timestamp' => '1234',
            'hmac' => '7dcea96ae5c9ca34f3197e9a48fa33fd40f2099c',
        );

        $cmd = "curl -H 'Content-Type: application/json' -d '" . CJSON::encode($params) . "' -X DELETE 'http://192.168.56.101/toptal_task/index-test.php/api/'";
        $output = shell_exec($cmd);
        $this->assertEquals('{"success"}', $output);

        $cmd = "curl 'http://192.168.56.101/toptal_task/index-test.php/api?client_id=" . $token->client_id . "&timestamp=1234&hmac=1c8c1a8e75c47409088204791807651939ec98f5' 3> /dev/null";
        $output = shell_exec($cmd);
        $this->assertEquals($expected_json_user1, $output);
    }
}
