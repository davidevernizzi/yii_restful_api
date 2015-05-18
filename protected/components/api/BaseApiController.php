<?php

class BaseApiController extends Controller
{
    const OK = 0;
    const TIMEOUT = -1;
    const BAD_AUTH_HEADER = -2;
    const BAD_HMAC = -3;
    const BAD_TOKEN = -4;
    const GENERIC_ERROR = -10;

    private $content;
    protected $data;
    protected $headers;
    protected $isCrud = false;

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

    // This must be implemented by the specific API
    protected function getHeaders()
    {
        return null;
    }

    // This must be implemented by the specific API
    protected function isAuthorised($headers)
    {
        return self::GENERIC_ERROR;
    }

    // TODO: handle recursive JSON objects
    // TODO: handle non-JSON API
    protected function beforeAction($action)
    {
        // 1. Get request data
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

        // 2. Get request headers -> by specific API
        $this->headers = $this->getHeaders();
        if ($this->headers == null) {
            echo ApiResponse::error('400', 'Bad Request');
            return false;
        }

        // 3. Check authorization -> by specific API
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
        case self::BAD_TOKEN:
            echo ApiResponse::error('401', 'Unauthorized');
            return false;
            break;
        case self::GENERIC_ERROR:
            echo ApiResponse::error('500', 'Internal Server Error');
            return false;
            break;
        default:
            echo ApiResponse::error('400', 'Bad Request (def 2)');
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
        if (!$this->isCrud) {
            echo ApiResponse::error('501', 'Not Implemented');
        }
        else {
            $model = new $this->isCrud();
            $crud_models = $model->findAll();
            foreach($crud_models as $model) {
                $crud[] = $model->attributes;
            }
            echo json_encode($crud);
        }
	}

    public function actionCreate()
    {
        if (!$this->isCrud) {
            echo ApiResponse::error('501', 'Not Implemented');
        }
        else {
            $model = new $this->isCrud();
            $data = json_decode($this->data, true);
            $model->attributes = $data;
            if (!$model->save()) {
                echo ApiResponse::error('503', 'Internal server error');
            }
            else {
                echo $model->id;
            }
        }
    }

    public function actionUpdate()
    {
        if (!$this->isCrud) {
            echo ApiResponse::error('501', 'Not Implemented');
        }
        else {
        }
    }

    public function actionDelete()
    {
        if (!$this->isCrud) {
            echo ApiResponse::error('501', 'Not Implemented');
        }
        else {
        }
    }
}
