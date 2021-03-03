<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;
use Exception;

class Auth extends BaseController
{
    public function login()
    {
        $rules = [
            'email' => 'required|valid_email|min_length[6]|max_length[50]',
            'password' => 'required|valid_email|min_length[8]|max_length[255]'
        ];
        $userVerify = false;

        $inputs = $this->getRequestInputs($this->request);

        if (!$this->validateRequest($inputs, $rules))
            return $this->sendResponse(
                [
                    'error' => true,
                    'errors' => $this->validator->getErrors(),
                ],
                ResponseInterface::HTTP_BAD_REQUEST
            );

        try {
            $userModel = new UserModel();
            $user = $userModel->findUserByField('email', $inputs['email']);
            $userVerify = password_verify($inputs['password'], $user['password']);
        } catch (Exception $e) {
            return $this->sendResponse(
                [
                    'error' => $e->getMessage(),
                    ResponseInterface::HTTP_BAD_REQUEST
                ]
            );
        }

        if ($userVerify === false)
            return $this->sendResponse(
                ['error' => 'Invalid login Credentials'],
                ResponseInterface::HTTP_BAD_REQUEST
            );

        return  $this->getJWTForUser($inputs['email']);
    }

    private function getJWTForUser(string $email, int $responseCode = ResponseInterface::HTTP_OK)
    {
        try {
            $userModel = new UserModel();
            $user = $userModel->findUserByField('email', $email);
            unset($user['password']);
            helper('jwt');
            return $this->sendResponse(
                [
                    'message' => 'User authenticated successfully',
                    'user' => $user,
                    'access_token' => getSignedJWTForUser($email)
                ]
            );
        } catch (Exception $e) {
            return $this->sendResponse(
                [
                    'error' => $e->getMessage(),
                    $responseCode
                ]
            );
        }
    }
}
