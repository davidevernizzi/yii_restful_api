<?php

class BaseApiController extends Controller
{
    const OK = 0;
    const TIMEOUT = -1;
    const BAD_AUTH_HEADER = -2;
    const BAD_HMAC = -3;

    private $content;
    protected $data;
    protected $headers;

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

        $this->headers = $this->getHeaders();
        if ($this->headers == null) {
            echo ApiResponse::error('400', 'Bad Request');
            return false;
        }

        return parent::beforeAction($action);
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
