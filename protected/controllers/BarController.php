<?php

class BarController extends HmacApiController
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
