<?php

namespace App\Modules\Staff\Controllers;

use App\Modules\Staff\Models\StaffModel;
use App\Modules\Staff\Models\DivisionsModel;
use App\Modules\Staff\Models\ServicesModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class StaffController extends Controller
{
    private StaffModel $model;
    private DivisionsModel $divisionsModel;
    private ServicesModel $servicesModel;

    public function __construct()
    {
        $this->model          = new StaffModel();
        $this->divisionsModel = new DivisionsModel();
        $this->servicesModel  = new ServicesModel();
    }

    // ----------------------------------------------------------------
    // Staff List
    // ----------------------------------------------------------------

    public function index(): string
    {
        $filters = [
            'search'      => $this->request->getGet('search') ?? '',
            'division_id' => $this->request->getGet('division_id') ?? '',
            'role'        => $this->request->getGet('role') ?? '',
            'is_active'   => $this->request->getGet('is_active') ?? '',
            'page'        => (int) ($this->request->getGet('page') ?? 1),
        ];

        $result     = $this->model->getList($filters);
        $totalPages = (int) ceil($result['total'] / $result['perPage']);
        $stats      = $this->model->getStats();
        $divisions  = $this->divisionsModel->getAll();
        $services   = $this->servicesModel->getAll();

        return view('App\Views\layouts\main', [
            'title'      => 'Manajemen Staff',
            'pageTitle'  => 'Manajemen Staff',
            'breadcrumb' => [['label' => 'Staff']],
            'content'    => view('App\Modules\Staff\Views\index', [
                'rows'       => $result['rows'],
                'total'      => $result['total'],
                'perPage'    => $result['perPage'],
                'page'       => $result['page'],
                'totalPages' => $totalPages,
                'filters'    => $filters,
                'stats'      => $stats,
                'divisions'  => $divisions,
                'services'   => $services,
            ]),
        ]);
    }

    // ----------------------------------------------------------------
    // Staff Detail
    // ----------------------------------------------------------------

    public function detail(int $id): string|RedirectResponse
    {
        $staff = $this->model->getDetail($id);

        if (! $staff) {
            return redirect()->to('/staff')->with('error', 'Staff tidak ditemukan.');
        }

        $ticketStats = $this->model->getTicketStats($id);
        $statusTab   = $this->request->getGet('status') ?? '';
        $tickets     = $this->model->getTickets($id, $statusTab);

        return view('App\Views\layouts\main', [
            'title'      => 'Detail Staff — ' . $staff['name'],
            'pageTitle'  => $staff['name'],
            'bodyClass'  => 'sidebar-collapse',
            'breadcrumb' => [
                ['label' => 'Staff', 'url' => base_url('staff')],
                ['label' => $staff['name']],
            ],
            'content' => view('App\Modules\Staff\Views\detail', [
                'staff'       => $staff,
                'ticketStats' => $ticketStats,
                'tickets'     => $tickets,
                'statusTab'   => $statusTab,
            ]),
        ]);
    }

    // ----------------------------------------------------------------
    // Staff Create / Store
    // ----------------------------------------------------------------

    public function create(): string
    {
        $divisions = $this->divisionsModel->getAll();

        return view('App\Views\layouts\main', [
            'title'      => 'Tambah Staff',
            'pageTitle'  => 'Tambah Staff',
            'breadcrumb' => [
                ['label' => 'Staff', 'url' => base_url('staff')],
                ['label' => 'Tambah Staff'],
            ],
            'content' => view('App\Modules\Staff\Views\form', [
                'staff'     => null,
                'divisions' => $divisions,
                'isEdit'    => false,
            ]),
        ]);
    }

    public function store(): RedirectResponse
    {
        $rules = [
            'name'        => 'required|min_length[3]|max_length[100]',
            'email'       => 'required|valid_email|max_length[150]',
            'password'    => 'required|min_length[6]',
            'role'        => 'required|in_list[admin,petugas]',
            'division_id' => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        if ($this->model->emailExists($email)) {
            return redirect()->back()->withInput()
                ->with('error', 'Email sudah digunakan oleh staff lain.');
        }

        $this->model->insert([
            'name'        => trim($this->request->getPost('name')),
            'email'       => strtolower(trim($email)),
            'password'    => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'        => $this->request->getPost('role'),
            'division_id' => (int) $this->request->getPost('division_id'),
            'is_active'   => 1,
        ]);

        return redirect()->to('/staff')->with('success', 'Staff berhasil ditambahkan.');
    }

    // ----------------------------------------------------------------
    // Staff Edit / Update
    // ----------------------------------------------------------------

    public function edit(int $id): string|RedirectResponse
    {
        $staff = $this->model->find($id);

        if (! $staff) {
            return redirect()->to('/staff')->with('error', 'Staff tidak ditemukan.');
        }

        $divisions = $this->divisionsModel->getAll();

        return view('App\Views\layouts\main', [
            'title'      => 'Edit Staff — ' . $staff['name'],
            'pageTitle'  => 'Edit Staff',
            'breadcrumb' => [
                ['label' => 'Staff', 'url' => base_url('staff')],
                ['label' => 'Edit ' . $staff['name']],
            ],
            'content' => view('App\Modules\Staff\Views\form', [
                'staff'     => $staff,
                'divisions' => $divisions,
                'isEdit'    => true,
            ]),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $staff = $this->model->find($id);

        if (! $staff) {
            return redirect()->to('/staff')->with('error', 'Staff tidak ditemukan.');
        }

        $rules = [
            'name'        => 'required|min_length[3]|max_length[100]',
            'email'       => 'required|valid_email|max_length[150]',
            'role'        => 'required|in_list[admin,petugas]',
            'division_id' => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        if ($this->model->emailExists($email, $id)) {
            return redirect()->back()->withInput()
                ->with('error', 'Email sudah digunakan oleh staff lain.');
        }

        $this->model->update($id, [
            'name'        => trim($this->request->getPost('name')),
            'email'       => strtolower(trim($email)),
            'role'        => $this->request->getPost('role'),
            'division_id' => (int) $this->request->getPost('division_id'),
        ]);

        return redirect()->to('/staff/detail/' . $id)->with('success', 'Data staff berhasil diperbarui.');
    }

    // ----------------------------------------------------------------
    // Toggle Active / Reset Password
    // ----------------------------------------------------------------

    public function toggle(int $id): RedirectResponse
    {
        $staff = $this->model->find($id);

        if (! $staff) {
            return redirect()->to('/staff')->with('error', 'Staff tidak ditemukan.');
        }

        // Prevent admin from deactivating own account
        if ($id === (int) session()->get('staff_id')) {
            return redirect()->back()->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        $newStatus = $staff['is_active'] ? 0 : 1;
        $this->model->update($id, ['is_active' => $newStatus]);

        $msg = $newStatus ? 'Staff berhasil diaktifkan.' : 'Staff berhasil dinonaktifkan.';
        return redirect()->back()->with('success', $msg);
    }

    public function resetPassword(int $id): RedirectResponse
    {
        $staff = $this->model->find($id);

        if (! $staff) {
            return redirect()->to('/staff')->with('error', 'Staff tidak ditemukan.');
        }

        $newPass = trim($this->request->getPost('new_password') ?? '');
        if (strlen($newPass) < 6) {
            return redirect()->back()->with('error', 'Password minimal 6 karakter.');
        }

        $this->model->update($id, [
            'password' => password_hash($newPass, PASSWORD_BCRYPT),
        ]);

        return redirect()->back()->with('success', 'Password staff berhasil direset.');
    }

    // ----------------------------------------------------------------
    // Divisions AJAX
    // ----------------------------------------------------------------

    public function divisionStore(): ResponseInterface
    {
        $name = trim($this->request->getPost('name') ?? '');
        $desc = trim($this->request->getPost('description') ?? '');

        if (empty($name)) {
            return $this->response->setStatusCode(422)
                ->setJSON(['success' => false, 'message' => 'Nama divisi wajib diisi.']);
        }

        if ($this->divisionsModel->nameExists($name)) {
            return $this->response->setStatusCode(422)
                ->setJSON(['success' => false, 'message' => 'Nama divisi sudah ada.']);
        }

        $id  = $this->divisionsModel->insert(['name' => $name, 'description' => $desc]);
        $row = $this->divisionsModel->find($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Divisi berhasil ditambahkan.',
            'row'     => $row,
            'csrf'    => csrf_hash(),
        ]);
    }

    public function divisionUpdate(int $id): ResponseInterface
    {
        $row = $this->divisionsModel->find($id);

        if (! $row) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Divisi tidak ditemukan.']);
        }

        $name = trim($this->request->getPost('name') ?? '');
        $desc = trim($this->request->getPost('description') ?? '');

        if (empty($name)) {
            return $this->response->setStatusCode(422)
                ->setJSON(['success' => false, 'message' => 'Nama divisi wajib diisi.']);
        }

        if ($this->divisionsModel->nameExists($name, $id)) {
            return $this->response->setStatusCode(422)
                ->setJSON(['success' => false, 'message' => 'Nama divisi sudah digunakan.']);
        }

        $this->divisionsModel->update($id, ['name' => $name, 'description' => $desc]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Divisi berhasil diperbarui.',
            'csrf'    => csrf_hash(),
        ]);
    }

    public function divisionDelete(int $id): ResponseInterface
    {
        $row = $this->divisionsModel->find($id);

        if (! $row) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Divisi tidak ditemukan.']);
        }

        // Check if used by staff or services
        $staffCount = $this->model->db->table('staff')->where('division_id', $id)->countAllResults();
        if ($staffCount > 0) {
            return $this->response->setStatusCode(422)
                ->setJSON(['success' => false, 'message' => 'Divisi tidak dapat dihapus karena masih memiliki ' . $staffCount . ' staff.']);
        }

        $svcCount = $this->model->db->table('service_categories')->where('divisions_id', $id)->countAllResults();
        if ($svcCount > 0) {
            return $this->response->setStatusCode(422)
                ->setJSON(['success' => false, 'message' => 'Divisi tidak dapat dihapus karena masih memiliki ' . $svcCount . ' layanan.']);
        }

        $this->divisionsModel->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Divisi berhasil dihapus.',
            'csrf'    => csrf_hash(),
        ]);
    }

    // ----------------------------------------------------------------
    // Services AJAX
    // ----------------------------------------------------------------

    public function serviceStore(): ResponseInterface
    {
        $name       = trim($this->request->getPost('name') ?? '');
        $divisionId = (int) ($this->request->getPost('divisions_id') ?? 0);

        if (empty($name) || $divisionId === 0) {
            return $this->response->setStatusCode(422)
                ->setJSON(['success' => false, 'message' => 'Nama layanan dan divisi wajib diisi.']);
        }

        $id  = $this->servicesModel->insert(['name' => $name, 'divisions_id' => $divisionId]);
        $row = $this->servicesModel->getAll();
        $newRow = array_values(array_filter($row, fn($r) => (int) $r['id'] === (int) $id))[0] ?? null;

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Layanan berhasil ditambahkan.',
            'row'     => $newRow,
            'csrf'    => csrf_hash(),
        ]);
    }

    public function serviceUpdate(int $id): ResponseInterface
    {
        $row = $this->servicesModel->find($id);

        if (! $row) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Layanan tidak ditemukan.']);
        }

        $name       = trim($this->request->getPost('name') ?? '');
        $divisionId = (int) ($this->request->getPost('divisions_id') ?? 0);

        if (empty($name) || $divisionId === 0) {
            return $this->response->setStatusCode(422)
                ->setJSON(['success' => false, 'message' => 'Nama layanan dan divisi wajib diisi.']);
        }

        $this->servicesModel->update($id, ['name' => $name, 'divisions_id' => $divisionId]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Layanan berhasil diperbarui.',
            'csrf'    => csrf_hash(),
        ]);
    }

    public function serviceDelete(int $id): ResponseInterface
    {
        $row = $this->servicesModel->find($id);

        if (! $row) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Layanan tidak ditemukan.']);
        }

        $ticketCount = $this->model->db->table('tickets')->where('service_category_id', $id)->countAllResults();
        if ($ticketCount > 0) {
            return $this->response->setStatusCode(422)
                ->setJSON(['success' => false, 'message' => 'Layanan tidak dapat dihapus karena digunakan oleh ' . $ticketCount . ' tiket.']);
        }

        $this->servicesModel->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Layanan berhasil dihapus.',
            'csrf'    => csrf_hash(),
        ]);
    }
}
