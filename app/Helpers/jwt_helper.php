<?php

use App\Models\UserModel;
use Config\Services;
use Firebase\JWT\JWT;

function getJWTFromRequest($authenticationHeader): string
{
    if (is_null($authenticationHeader))
        throw new Exception('Missing or invalid JWT in request');
    return explode(' ', $authenticationHeader)[1];
}

function validateJWTFromRequest(string $encodeToken)
{
    $JWTKey = Services::getJWTSecretKey();
    $decodedToken = JWT::decode($encodeToken, $JWTKey, ['HS256']);
    $userModel = new UserModel();
    return $userModel->findUserByField('email', $decodedToken->email);
}

function getSignedJWTForUser(string $email)
{
    $issuedAtTime = time();
    $tokenTimeToLive = getenv('JWT_TIME_TO_LIVE');
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $payload = [
        'email' => $email,
        'iat' => $issuedAtTime,
        'exp' => $tokenExpiration
    ];

    $jwt = JWT::encode($payload, Services::getJWTSecretKey());
    return $jwt;
}
