<?php

namespace App\Filters;

use App\Libraries\JWTLibrary;
use App\Models\User;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class JWTAuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $jwt = new JWTLibrary();
        $userModel = new User();

        $header = $request->getHeaderLine('Authorization');

        if (empty($header) || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return service('response')->setJSON([
                'status' => 401,
                'message' => 'Access token is required'
            ])->setStatusCode(401);
        }

        $token = $matches[1];
        $payload = $jwt->validateToken($token);

        if (!$payload) {
            return service('response')->setJSON([
                'status' => 401,
                'message' => 'Invalid or expired token'
            ])->setStatusCode(401);
        }

        // Verifikasi token ada di database
        $user = $userModel->getUserByToken($token);
        if (!$user) {
            return service('response')->setJSON([
                'status' => 401,
                'message' => 'Token not found'
            ])->setStatusCode(401);
        }

        $request->user = $user;

        return $request;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
