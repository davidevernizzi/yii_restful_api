<?php

class BarController extends ApiController
{
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
        echo 'GET bar';
	}

    public function actionCreate()
    {
        echo 'POST bar';
    }

    public function actionUpdate()
    {
        echo 'PUT bar';
    }

    public function actionDelete()
    {
        echo 'DELETE bar';
    }
}
