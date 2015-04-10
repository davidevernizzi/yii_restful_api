<?php

class BarController extends ApiController
{
	public function actionIndex()
	{
        $this->mock();
	}

    public function actionCreate()
    {
        $this->mock();
    }

    public function actionUpdate()
    {
        $this->mock();
    }

    public function actionDelete()
    {
        echo 'DELETE bar';
    }
}
