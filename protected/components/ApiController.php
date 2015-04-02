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

        // TODO: refactor below
        // Get headers
        $headers =  apache_request_headers();
        if (!isset($headers['Authorization'])) {
            echo ApiResponse::error('400', 'Bad Request');
            return false;
        }
        if (!isset($headers['Timestamp'])) {
            echo ApiResponse::error('400', 'Bad Request');
            return false;
        }

        // Check timestamp
        $timestamp = $headers['Timestamp'];
        $allowedWindow = 3600; // 1 hour. TODO: get this from config
        if(abs($timestamp - time()) > $allowedWindow) {
            echo ApiResponse::error('401', 'Unauthorized');
            return false;
        }

        // Check HMAC
        $auth = array();
        preg_match('/^hmac ([^:]*):([^:]*)$/', $headers['Authorization'], $auth);
        $resource = $_SERVER['REQUEST_METHOD'] . Yii::app()->controller->id;
        if (!is_array($auth) || count($auth) != 3) {
            echo ApiResponse::error('400', 'Bad Request');
            return false;
        }
        $secret = 'xxx'; // TODO: fetch secret from tbl_api_token using $auth[1] as search criteria
        $hmac = $auth[2];
        if(!Hmac::verify($hmac, $timestamp, $secret, $this->data, $resource)) {
            echo ApiResponse::error('401', 'Unauthorized');
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
