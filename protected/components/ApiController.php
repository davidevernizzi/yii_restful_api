<?php

class ApiController extends Controller
{
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

        $headers =  apache_request_headers();
        echo $headers['Authorization'];

        /* TODO:
         * 0. fetch secret from tbl_api_token
         * 1. compute hash of the request
         * 2. match hash with header hash
         * 3. verify timestamp
         * 4. veri
        /*
        var_dump($headers);
        var_dump($_SERVER);
         */
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
