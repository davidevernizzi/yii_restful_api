<?php

class SafeController extends SafeApiController
{
    public $defaultAction = 'index';

    public function actionIndex()
    {
        echo 'GET safe';
    }
}
