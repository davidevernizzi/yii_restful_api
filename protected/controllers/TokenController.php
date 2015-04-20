<?php

class TokenController extends TokenApiController
{
	public function actionIndex()
	{
        echo 'GET token';
	}

    public function actionUpdate()
    {
        echo 'PUT token';
    }

    public function actionDelete()
    {
        echo 'DELETE token';
    }
}
