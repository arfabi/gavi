<?php

namespace App\Modules\Customers\Controllers;

use App\Modules\Customers\Models\CustomersModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;

class CustomersController extends Controller
{
    private CustomersModel $model;
    private bool $isAdmin;

    public function __construct()
    {
        $this->model   = new CustomersModel();
        $this->isAdmin = session()->get('role') === 'admin';
    }

    public function index(): string
    {
        $filters = [
            'search'   => $this->request->getGet('search') ?? '',
            'ai_mode'  => $this->request->getGet('ai_mode') ?? '',
            'page'     => (int) ($this->request->getGet('page') ?? 1),
        ];

        $result     = $this->model->getList($filters);
        $stats      = $this->model->getStats();
        $totalPages = (int) ceil($result['total'] / $result['perPage']);

        return view('App\Views\layouts\main', [
            'title'      => 'Data Customer',
            'pageTitle'  => 'Data Customer',
            'breadcrumb' => [['label' => 'Data Customer']],
            'content'    => view('App\Modules\Customers\Views\index', [
                'rows'       => $result['rows'],
                'total'      => $result['total'],
                'perPage'    => $result['perPage'],
                'page'       => $result['page'],
                'totalPages' => $totalPages,
                'filters'    => $filters,
                'stats'      => $stats,
                'isAdmin'    => $this->isAdmin,
            ]),
        ]);
    }

    public function detail(int $id): string|RedirectResponse
    {
        $customer = $this->model->getDetail($id);

        if (! $customer) {
            return redirect()->to('/customers')->with('error', 'Customer tidak ditemukan.');
        }

        $sessions  = $this->model->getSessions($id);
        $tickets   = $this->model->getTickets($id);

        // Default: semua chat, atau filter per session
        $sessionId = $this->request->getGet('session_id') ? (int) $this->request->getGet('session_id') : null;
        $chats     = $this->model->getChatLogs($id, $sessionId);

        return view('App\Views\layouts\main', [
            'title'      => 'Detail Customer: ' . ($customer['name'] ?? $customer['whatsapp_number']),
            'pageTitle'  => $customer['name'] ?? $customer['whatsapp_number'],
            'bodyClass'  => 'sidebar-collapse',
            'breadcrumb' => [
                ['label' => 'Data Customer', 'url' => base_url('customers')],
                ['label' => $customer['name'] ?? $customer['whatsapp_number']],
            ],
            'content' => view('App\Modules\Customers\Views\detail', [
                'customer'  => $customer,
                'sessions'  => $sessions,
                'chats'     => $chats,
                'tickets'   => $tickets,
                'sessionId' => $sessionId,
                'isAdmin'   => $this->isAdmin,
            ]),
        ]);
    }
}
