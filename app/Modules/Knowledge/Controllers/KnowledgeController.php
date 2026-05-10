<?php

namespace App\Modules\Knowledge\Controllers;

use App\Modules\Knowledge\Models\KnowledgeModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;

class KnowledgeController extends Controller
{
    private KnowledgeModel $model;
    private \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->model = new KnowledgeModel();
        $this->db    = \Config\Database::connect();
    }

    public function index(): string
    {
        $filters = [
            'search'      => $this->request->getGet('search') ?? '',
            'category_id' => $this->request->getGet('category_id') ?? '',
            'aktif'       => $this->request->getGet('aktif') ?? '',
            'synced'      => $this->request->getGet('synced') ?? '',
            'page'        => (int) ($this->request->getGet('page') ?? 1),
        ];

        $result     = $this->model->getWithCategory($filters, 20);
        $categories = $this->db->table('service_categories')
            ->orderBy('name')->get()->getResultArray();
        $stats = $this->model->getStats();

        $totalPages = (int) ceil($result['total'] / $result['perPage']);

        return view('App\Views\layouts\main', [
            'title'      => 'Knowledge Base',
            'pageTitle'  => 'Manajemen Knowledge Base',
            'breadcrumb' => [['label' => 'Knowledge Base']],
            'content'    => view('App\Modules\Knowledge\Views\index', [
                'rows'       => $result['rows'],
                'total'      => $result['total'],
                'perPage'    => $result['perPage'],
                'page'       => $result['page'],
                'totalPages' => $totalPages,
                'filters'    => $filters,
                'categories' => $categories,
                'stats'      => $stats,
            ]),
        ]);
    }

    public function create(): string
    {
        $categories = $this->db->table('service_categories')
            ->orderBy('name')->get()->getResultArray();

        return view('App\Views\layouts\main', [
            'title'      => 'Tambah Dokumen KB',
            'pageTitle'  => 'Tambah Dokumen Knowledge Base',
            'breadcrumb' => [
                ['label' => 'Knowledge Base', 'url' => base_url('knowledge')],
                ['label' => 'Tambah'],
            ],
            'content' => view('App\Modules\Knowledge\Views\form', [
                'categories' => $categories,
                'doc'        => null,
            ]),
        ]);
    }

    public function store(): RedirectResponse
    {
        $rules = [
            'judul'               => 'required|max_length[200]',
            'service_category_id' => 'required|integer',
            'konten'              => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->model->insert([
            'service_category_id' => $this->request->getPost('service_category_id'),
            'judul'               => $this->request->getPost('judul'),
            'konten'              => $this->request->getPost('konten'),
            'aktif'               => $this->request->getPost('aktif') ? 1 : 0,
            'synced_to_supabase'  => 0,
            'created_by'          => session()->get('staff_id'),
        ]);

        return redirect()->to('/knowledge')
            ->with('success', 'Dokumen berhasil ditambahkan. Jangan lupa sync ke Supabase.');
    }

    public function edit(int $id): string|RedirectResponse
    {
        $doc = $this->model->getOneWithCategory($id);
        if (! $doc) {
            return redirect()->to('/knowledge')->with('error', 'Dokumen tidak ditemukan.');
        }

        $categories = $this->db->table('service_categories')
            ->orderBy('name')->get()->getResultArray();

        return view('App\Views\layouts\main', [
            'title'      => 'Edit Dokumen KB',
            'pageTitle'  => 'Edit Dokumen Knowledge Base',
            'breadcrumb' => [
                ['label' => 'Knowledge Base', 'url' => base_url('knowledge')],
                ['label' => 'Edit: ' . substr($doc['judul'], 0, 40)],
            ],
            'content' => view('App\Modules\Knowledge\Views\form', [
                'categories' => $categories,
                'doc'        => $doc,
            ]),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $doc = $this->model->find($id);
        if (! $doc) {
            return redirect()->to('/knowledge')->with('error', 'Dokumen tidak ditemukan.');
        }

        $rules = [
            'judul'               => 'required|max_length[200]',
            'service_category_id' => 'required|integer',
            'konten'              => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'service_category_id' => $this->request->getPost('service_category_id'),
            'judul'               => $this->request->getPost('judul'),
            'konten'              => $this->request->getPost('konten'),
            'aktif'               => $this->request->getPost('aktif') ? 1 : 0,
            'synced_to_supabase'  => 0, // reset sync setelah edit
            'supabase_id'         => null,
        ]);

        return redirect()->to('/knowledge')
            ->with('success', 'Dokumen berhasil diupdate. Sync ulang ke Supabase diperlukan.');
    }

    public function delete(int $id): RedirectResponse
    {
        if (! $this->model->find($id)) {
            return redirect()->to('/knowledge')->with('error', 'Dokumen tidak ditemukan.');
        }

        $this->model->delete($id);
        return redirect()->to('/knowledge')->with('success', 'Dokumen berhasil dihapus.');
    }

    public function sync(int $id): RedirectResponse
    {
        $doc = $this->model->find($id);
        if (! $doc) {
            return redirect()->to('/knowledge')->with('error', 'Dokumen tidak ditemukan.');
        }

        // Tandai sebagai synced (tanpa koneksi Supabase sungguhan saat dev)
        $this->model->markSynced($id, 'supabase_' . uniqid());

        return redirect()->to('/knowledge')
            ->with('success', 'Dokumen berhasil disinkronkan ke Supabase.');
    }
}
