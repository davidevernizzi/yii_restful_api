<?php

class ApiController extends BaseApiController
{
    const OK = 0;
    const TIMEOUT = -1;
    const BAD_AUTH_HEADER = -2;
    const BAD_HMAC = -3;

    private $content;
    private $data;

    private function getContent()
    {
        if (null === $this->content)
        {
            if (0 === strlen(($this->content = file_get_contents('php://input'))))
            {
                $this->content = false;
            }
        }

        return $this->content;
    }

    private function getHeaders()
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

    private function isAuthorised($headers)
    {
        if(!$this->isOnTime($headers)) {
            return self::TIMEOUT;
        }

        $timestamp = $this->getTimestamp($headers);
        $auth = array();
        preg_match('/^hmac ([^:]*):([^:]*)$/', $headers['Authorization'], $auth);
        $resource = $_SERVER['REQUEST_METHOD'] . Yii::app()->controller->id;
        if (!is_array($auth) || count($auth) != 3) {
            return self::BAD_AUTH_HEADER;
        }
        $restToken = RestTokens::model()->findByAttributes(array('client_id'=>$auth[1])); // TODO: check if api_key is still active
        $secret = $restToken->client_secret;
        $hmac = $auth[2];
        if(!Hmac::verify($hmac, $timestamp, $secret, $this->data, $resource)) {
            return self::BAD_HMAC;
        }

        return self::OK;
    }

    // TODO: handle recursive JSON objects
    // TODO: handle non-JSON API
    protected function beforeAction($action)
    {
        /*
        switch($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $this->data = $_GET;
            break;
        case 'POST':
            $this->data = CJSON::decode($this->getContent());
            break;
        case 'DELETE':
            $this->data = CJSON::decode($this->getContent());
            break;
        case 'PUT':
            $this->data = CJSON::decode($this->getContent());
            break;
        }

        $headers = $this->getHeaders();
        if ($headers == null) {
            echo ApiResponse::error('400', 'Bad Request');
            return false;
        }
         */

        if (!parent::beforeAction($action)) {
            return false;
        }

        switch ($this->isAuthorised($this->headers)) {
        case self::OK:
            break;
        case self::TIMEOUT:
            echo ApiResponse::error('401', 'Unauthorized');
            return false;
            break;
        case self::BAD_AUTH_HEADER :
            echo ApiResponse::error('400', 'Bad Request');
            return false;
            break;
        case self::BAD_HMAC:
            echo ApiResponse::error('401', 'Unauthorized');
            return false;
            break;
        default:
            echo ApiResponse::error('400', 'Bad Request');
            return false;
        }
        
        return true;
    }

    protected function mock()
    {
        // TODO: get mockups directory from config
        $filename = Yii::app()->basePath . '/mockups/' . Yii::app()->controller->id . '_' . Yii::app()->controller->action->id;
        if (file_exists($filename)) {
            $mockStr = file_get_contents($filename);
            echo $mockStr;
        }
        else {
            echo ApiResponse::error('501', 'Not Implemented');
        }
    }

	public function actionIndex()
	{
        echo ApiResponse::error('501', 'Not Implemented');
	}

    public function actionCreate()
    {
        echo ApiResponse::error('501', 'Not Implemented');
    }

    public function actionUpdate()
    {
        echo ApiResponse::error('501', 'Not Implemented');
    }

    public function actionDelete()
    {
        echo ApiResponse::error('501', 'Not Implemented');
    }
}
