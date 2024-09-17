<?php

namespace app\controllers;

use app\models\User;
use app\services\JwtService;
use Yii;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

class AuthController extends Controller
{
    public $enableCsrfValidation = false;
    private $jwtService;

    public function __construct($id, $module, JwtService $jwtService, $config = [])
    {
        $this->jwtService = $jwtService;
        parent::__construct($id, $module, $config);
    }

    public function actionLogin()
    {
        $request = Yii::$app->request;
        $username = $request->post('username');
        $password = $request->post('password');

        if (!$username || !$password) {
            return $this->asJson([
                'Error' => 'Parâmetros de login inválidos',
            ]);
        }

        $user = User::findByUsername($username);

        if (!$user || !$user->validatePassword($password)) {
            return $this->asJson([
                'Error' => 'Credenciais inválidas',
            ]);
        }

        $tokens = $this->jwtService->generateTokens($user);

        return $this->asJson([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
        ]);
    }

    public function actionRefreshToken()
    {
        $request = Yii::$app->request;
        $refreshToken = $request->post('refresh_token');

        try {
            $jwtService = new JwtService();
            $payload = $jwtService->validateToken($refreshToken);

            $user = User::findOne($payload['sub']);
            if (!$user) {
                throw new UnauthorizedHttpException('Invalid refresh token');
            }

            $tokens = $this->jwtService->generateTokens($user);

            return $this->asJson([
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
            ]);
        } catch (\Exception $e) {
            throw new UnauthorizedHttpException('Invalid refresh token');
        }
    }
}
