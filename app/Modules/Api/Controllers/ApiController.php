<?php

namespace App\Modules\Api\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class ApiController extends Controller
{
    public function log(): ResponseInterface
    {
        return $this->response->setJSON(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function ticket(): ResponseInterface
    {
        return $this->response->setJSON(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function setting(string $key): ResponseInterface
    {
        return $this->response->setJSON(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function customer(): ResponseInterface
    {
        return $this->response->setJSON(['success' => false, 'message' => 'Not implemented yet']);
    }
}
