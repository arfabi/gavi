<?php

namespace App\Modules\Dashboard\Controllers;

use CodeIgniter\Controller;

class DashboardController extends Controller
{
    public function index(): string
    {
        $data = [
            'title'     => 'Dashboard',
            'pageTitle' => 'Dashboard Overview',
            'breadcrumb' => [['label' => 'Dashboard']],
            'content'   => '<div class="alert alert-info"><i class="fas fa-info-circle me-1"></i> Modul Dashboard sedang dalam pengembangan.</div>',
        ];

        return view('App\Views\layouts\main', $data);
    }

    public function analytics(): string
    {
        $data = [
            'title'     => 'Analytics',
            'pageTitle' => 'Analytics',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => base_url('dashboard')],
                ['label' => 'Analytics'],
            ],
            'content' => '<div class="alert alert-info"><i class="fas fa-info-circle me-1"></i> Modul Analytics sedang dalam pengembangan.</div>',
        ];

        return view('App\Views\layouts\main', $data);
    }
}
