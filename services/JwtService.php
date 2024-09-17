<?php

namespace app\services;

use InvalidArgumentException;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\HS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Yii;

class JwtService
{
    private $jwk;

    public function __construct()
    {
        $secretKey = Yii::$app->params['jwtSecret'];
        $this->jwk = JWKFactory::createFromSecret($secretKey);
    }

    public function generateTokens($user)
    {
        $accessToken = $this->generateAccessToken($user);
        $refreshToken = $this->generateRefreshToken($user);

        $user->access_token = $accessToken;
        $user->refresh_token = $refreshToken;
        $user->save();

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }

    public function generateAccessToken($user)
    {
        $algorithmManager = new AlgorithmManager([new HS256()]);
        $jwsBuilder = new JWSBuilder($algorithmManager);
        $payload = json_encode([
            'sub' => $user->id,
            'username' => $user->username,
            'iat' => time(),
            'exp' => time() + 3600,
        ], JSON_THROW_ON_ERROR);

        $jws = $jwsBuilder
            ->create()
            ->withPayload($payload)
            ->addSignature($this->jwk, ['alg' => 'HS256'])
            ->build();

        return (new CompactSerializer())->serialize($jws);
    }

    private function generateRefreshToken($user)
    {
        $algorithmManager = new AlgorithmManager([new HS256()]);
        $jwsBuilder = new JWSBuilder($algorithmManager);

        $payload = json_encode([
            'sub' => $user->id,
            'username' => $user->username,
            'iat' => time(),
            'exp' => time() + (3600 * 24 * 7),
        ], JSON_THROW_ON_ERROR);

        $jws = $jwsBuilder
            ->create()
            ->withPayload($payload)
            ->addSignature($this->jwk, ['alg' => 'HS256'])
            ->build();

        return (new CompactSerializer())->serialize($jws);
    }
    public function validateToken($token)
    {
        $algorithmManager = new AlgorithmManager([new HS256()]);
        $serializer = new CompactSerializer();
        $jws = $serializer->unserialize($token);

        $jwsVerifier = new JWSVerifier($algorithmManager);
        $isValid = $jwsVerifier->verifyWithKey($jws, $this->jwk, 0);

        if (!$isValid) {
            throw new InvalidArgumentException('Invalid token');
        }

        return json_decode($jws->getPayload(), true, 512, JSON_THROW_ON_ERROR);
    }
}
