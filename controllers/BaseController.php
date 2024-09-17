<?php

namespace app\controllers;

use yii\filters\auth\HttpBearerAuth;

class BaseController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        return $behaviors;
    }
}
