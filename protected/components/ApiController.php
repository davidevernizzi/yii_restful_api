<?php

class ApiController extends Controller
{
	public function actionIndex()
	{
        echo $this->error('301', 'Method not available');
	}

    public function actionCreate()
    {
        echo $this->error('301', 'Method not available');
    }

    public function actionUpdate()
    {
        echo $this->error('301', 'Method not available');
    }

    public function actionDelete()
    {
        echo $this->error('301', 'Method not available');
    }

    public function error($errorCode='', $errorMessage='Generic error')
    {
        return CJSON::encode(array(
            'code' => $errorCode,
            'error' => $errorMessage,
        ));
    }
}
