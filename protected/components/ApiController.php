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
        $headers =  apache_request_headers();
        $timestamp = $headers['Timestamp'];
        $auth = array();
        preg_match('/^hmac ([^:]*):([^:]*)$/', $headers['Authorization'], $auth);
        $secret = 'xxx'; // TODO: fetch secret from tbl_api_token using $auth[1] as search criteria
        $hmac = $auth[2];
        $resource = $_SERVER['REQUEST_METHOD'] . Yii::app()->controller->id;

        if(!Hmac::verify($hmac, $timestamp, $secret, $this->data, $resource)) {
            http_response_code(401);
            echo "{401: 'Unauthorized'}";
            return false;
        }
        
        // TODO: verify timestamp

        return true;
    }
	public function actionIndex()
	{
        echo $this->error('501', 'Not Implemented');
	}

    public function actionCreate()
    {
        echo $this->error('501', 'Not Implemented');
    }

    public function actionUpdate()
    {
        echo $this->error('501', 'Not Implemented');
    }

    public function actionDelete()
    {
        echo $this->error('501', 'Not Implemented');
    }

    public function error($errorCode='', $errorMessage='Generic error')
    {
        http_response_code($errorCode);
        return CJSON::encode(array(
            'code' => $errorCode,
            'error' => $errorMessage,
        ));
    }
}
