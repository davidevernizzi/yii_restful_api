<?php

class ApiController extends Controller
{
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
            return false;
        }

        $timestamp = $this->getTimestamp($headers);
        $auth = array();
        preg_match('/^hmac ([^:]*):([^:]*)$/', $headers['Authorization'], $auth);
        $resource = $_SERVER['REQUEST_METHOD'] . Yii::app()->controller->id;
        if (!is_array($auth) || count($auth) != 3) {
            return -1;
        }
        $restToken = RestTokens::model()->findByAttributes(array('client_id'=>$auth[1]));
        $secret = $restToken->client_secret;
        $hmac = $auth[2];
        if(!Hmac::verify($hmac, $timestamp, $secret, $this->data, $resource)) {
            return -2;
        }

        return 0;
    }

    // TODO: handle recursive JSON objects
    // TODO: handle non-JSON API
    protected function beforeAction($action)
    {
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

        switch ($this->isAuthorised($headers)) {
        case 0:
            break;
        case OUT_OF_TIME;
            echo ApiResponse::error('401', 'Unauthorized');
            return false;
            break;
        case -1:
            echo ApiResponse::error('400', 'Bad Request');
            return false;
            break;
        case -2:
            echo ApiResponse::error('401', 'Unauthorized');
            return false;
            break;
        default:
            echo ApiResponse::error('400', 'Bad Request');
            return false;
        }
        
        return true;
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
