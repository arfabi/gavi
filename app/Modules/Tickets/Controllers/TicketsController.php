<?php

namespace App\Modules\Tickets\Controllers;

use App\Modules\Tickets\Models\TicketsModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class TicketsController extends Controller
{
    private TicketsModel $model;
    private \CodeIgniter\Database\BaseConnection $db;
    private bool $isAdmin;
    private int $staffId;

    public function __construct()
    {
        $this->model   = new TicketsModel();
        $this->db      = \Config\Database::connect();
        $this->isAdmin = session()->get('role') === 'admin';
        $this->staffId = (int) session()->get('staff_id');
    }

    public function index(): string
    {
        $filters = [
            'status'   => $this->request->getGet('status') ?? '',
            'priority' => $this->request->getGet('priority') ?? '',
            'search'   => $this->request->getGet('search') ?? '',
            'page'     => (int) ($this->request->getGet('page') ?? 1),
        ];

        $result  = $this->model->getList($filters, $this->isAdmin, $this->staffId);
        $counts  = $this->model->countByStatus($this->isAdmin, $this->staffId);
        $totalPages = (int) ceil($result['total'] / $result['perPage']);

        return view('App\Views\layouts\main', [
            'title'      => 'Tiket Eskalasi',
            'pageTitle'  => 'Inbox Tiket',
            'breadcrumb' => [['label' => 'Tiket Eskalasi']],
            'content'    => view('App\Modules\Tickets\Views\index', [
                'rows'       => $result['rows'],
                'total'      => $result['total'],
                'perPage'    => $result['perPage'],
                'page'       => $result['page'],
                'totalPages' => $totalPages,
                'filters'    => $filters,
                'counts'     => $counts,
                'isAdmin'    => $this->isAdmin,
            ]),
        ]);
    }

    public function detail(int $id): string|RedirectResponse
    {
        $ticket = $this->model->getDetail($id);

        if (! $ticket) {
            return redirect()->to('/tickets')->with('error', 'Tiket tidak ditemukan.');
        }

        // Petugas hanya bisa lihat tiket assigned ke dia atau unassigned
        if (
            ! $this->isAdmin
            && $ticket['assigned_staff_id'] !== null
            && (int) $ticket['assigned_staff_id'] !== $this->staffId
        ) {
            return redirect()->to('/tickets')->with('error', 'Akses ditolak.');
        }

        $chats = $this->model->getChatLogs($id);

        // Staff list untuk reassign (admin saja)
        $staffList = [];
        if ($this->isAdmin) {
            $staffList = $this->db->table('staff')
                ->select('id, name, role')
                ->where('is_active', 1)
                ->orderBy('name')
                ->get()->getResultArray();
        }

        $templates = [];
        if (! empty($ticket['service_category_id'])) {
            $templates = $this->model->getTemplates((int) $ticket['service_category_id']);
        }

        return view('App\Views\layouts\main', [
            'title'      => 'Detail Tiket ' . $ticket['ticket_number'],
            'pageTitle'  => "Detail Tiket " . $ticket['ticket_number'],
            'bodyClass'  => 'sidebar-collapse',
            'breadcrumb' => [
                ['label' => 'Tiket Eskalasi', 'url' => base_url('tickets')],
                ['label' => $ticket['ticket_number']],
            ],
            'content' => view('App\Modules\Tickets\Views\detail', [
                'ticket'    => $ticket,
                'chats'     => $chats,
                'isAdmin'   => $this->isAdmin,
                'staffId'   => $this->staffId,
                'staffList' => $staffList,
                'templates' => $templates,
            ]),
        ]);
    }

    public function assign(int $id): RedirectResponse
    {
        $ticket = $this->model->find($id);

        if (! $ticket) {
            return redirect()->to('/tickets')->with('error', 'Tiket tidak ditemukan.');
        }

        if (in_array($ticket['status'], ['resolved', 'closed'])) {
            return redirect()->back()->with('error', 'Tiket sudah selesai, tidak bisa di-assign.');
        }

        $this->model->assign($id, $this->staffId);
        return redirect()->back()->with('success', 'Tiket berhasil diambil.');
    }

    public function resolve(int $id): RedirectResponse
    {
        $ticket = $this->model->find($id);

        if (! $ticket) {
            return redirect()->to('/tickets')->with('error', 'Tiket tidak ditemukan.');
        }

        if (! $this->isAdmin && (int) $ticket['assigned_staff_id'] !== $this->staffId) {
            return redirect()->back()->with('error', 'Anda tidak bisa menyelesaikan tiket ini.');
        }

        $catatan = trim($this->request->getPost('catatan_petugas') ?? '');
        if (empty($catatan)) {
            return redirect()->back()->with('error', 'Catatan petugas wajib diisi sebelum menyelesaikan tiket.');
        }

        $this->model->resolve($id, $catatan);
        return redirect()->to('/tickets')->with('success', 'Tiket ' . $ticket['ticket_number'] . ' berhasil diselesaikan.');
    }

    public function reply(int $id): ResponseInterface
    {
        $ticket = $this->model->find($id);

        if (! $ticket) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Tiket tidak ditemukan.']);
        }

        if (! $this->isAdmin && (int) $ticket['assigned_staff_id'] !== $this->staffId) {
            return $this->response->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $message = trim($this->request->getPost('message') ?? '');
        if (empty($message)) {
            return $this->response->setStatusCode(422)
                ->setJSON(['success' => false, 'message' => 'Pesan tidak boleh kosong.']);
        }

        $chat = $this->model->insertReply($id, $this->staffId, $message);

        return $this->response->setJSON([
            'success'    => true,
            'chat'       => $chat,
            'staff_name' => session()->get('name'),
            'csrf'       => csrf_hash(),
        ]);
    }

    public function templates(): ResponseInterface
    {
        $keyword    = trim($this->request->getGet('q') ?? '');
        $categoryId = (int) ($this->request->getGet('category_id') ?? 0);

        $templates = $this->model->searchTemplates($keyword, $categoryId);

        return $this->response->setJSON([
            'success'   => true,
            'templates' => $templates,
        ]);
    }

    public function poll(): ResponseInterface
    {
        $counts = $this->model->countByStatus($this->isAdmin, $this->staffId);
        return $this->response->setJSON([
            'success' => true,
            'counts'  => $counts,
            'total'   => array_sum($counts),
        ]);
    }
}
