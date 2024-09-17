<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\User;

class UserController extends \yii\web\Controller
{
    /**
     * Comando para criar um usuário via terminal
     * @param string $username
     * @param string $password
     * @param string $name
     */

    public function actionCreate($username, $password, $name)
    {
        $user = new User();
        $user->username = $username;
        $user->name = $name;
        $user->setPassword($password);
        $user->generateAuthKey();

        if ($user->save()) {
            echo "Usuário {$username} criado com sucesso.\n";
        } else {
            echo "Erro ao criar usuário: \n";
            print_r($user->errors);
        }
    }

    public function beforeAction($action)
    {
        if (Yii::$app instanceof \yii\web\Application) {
            return parent::beforeAction($action); // Aplica validação de CSRF somente para web
        }
        return true; // Pula para requisições do console
    }


}
