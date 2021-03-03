<?php

namespace App\Filters;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\RequestTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Services;
use Exception;

class JWTAuthenticationFilter implements FilterInterface
{
    use RequestTrait;

    public function before(RequestInterface $request, $arguments = null)
    {
        $authorizationHeader = $request->getServer('HTTP_AUTHORIZATION');
        try {
            helper('jwt');
            $encodedToken = getJWTFromRequest($$authorizationHeader);
            validateJWTFromRequest($encodedToken);
            return $request;
        } catch (Exception $e) {
            return Services::response()
                ->setJson(
                    ['error' => $e->getMessage()]
                )
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }


    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        # code...
    }
}
