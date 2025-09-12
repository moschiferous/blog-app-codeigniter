<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class HomeController extends BaseController
{
    use ResponseTrait;

    public function index(): ResponseInterface
    {
        return $this->respond([
            'status' => 200,
            'message' => 'Connection Good'
        ]);
    }
}
