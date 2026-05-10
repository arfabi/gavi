<?php

namespace App\Modules\Staff\Models;

use CodeIgniter\Model;

class StaffModel extends Model
{
    protected $table      = 'staff';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'division_id', 'name', 'email', 'password',
        'role', 'is_active', 'last_login',
    ];

    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'update_at';

    public function getList(array $filters, int $perPage = 20): array
    {
        $builder = $this->db->table('staff s')
            ->select('s.id, s.name, s.email, s.role, s.is_active, s.last_login, s.created_at,
                      d.name as division_name,
                      (SELECT COUNT(*) FROM tickets WHERE assigned_staff_id = s.id) as total_tickets,
                      (SELECT COUNT(*) FROM tickets WHERE assigned_staff_id = s.id AND status IN ("open","pending")) as open_tickets')
            ->join('divisions d', 'd.id = s.division_id', 'left');

        if (! empty($filters['search'])) {
            $builder->groupStart()
                ->like('s.name', $filters['search'])
                ->orLike('s.email', $filters['search'])
                ->groupEnd();
        }

        if (! empty($filters['division_id'])) {
            $builder->where('s.division_id', $filters['division_id']);
        }

        if ($filters['role'] ?? '' !== '') {
            $builder->where('s.role', $filters['role']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $builder->where('s.is_active', (int) $filters['is_active']);
        }

        $builder->orderBy('s.name');

        $total  = $builder->countAllResults(false);
        $page   = (int) ($filters['page'] ?? 1);
        $offset = ($page - 1) * $perPage;
        $rows   = $builder->limit($perPage, $offset)->get()->getResultArray();

        return ['rows' => $rows, 'total' => $total, 'perPage' => $perPage, 'page' => $page];
    }

    public function getDetail(int $id): ?array
    {
        return $this->db->table('staff s')
            ->select('s.*, d.name as division_name')
            ->join('divisions d', 'd.id = s.division_id', 'left')
            ->where('s.id', $id)
            ->get()->getRowArray() ?: null;
    }

    public function getStats(): array
    {
        $total   = $this->db->table('staff')->countAllResults();
        $admin   = $this->db->table('staff')->where('role', 'admin')->countAllResults();
        $petugas = $this->db->table('staff')->where('role', 'petugas')->countAllResults();
        $active  = $this->db->table('staff')->where('is_active', 1)->countAllResults();

        return compact('total', 'admin', 'petugas', 'active');
    }

    public function getTicketStats(int $staffId): array
    {
        $statuses = ['open', 'pending', 'resolved', 'closed'];
        $result   = ['total' => 0];

        foreach ($statuses as $s) {
            $count = $this->db->table('tickets')
                ->where('assigned_staff_id', $staffId)
                ->where('status', $s)
                ->countAllResults();
            $result[$s] = $count;
            $result['total'] += $count;
        }

        return $result;
    }

    public function getTickets(int $staffId, string $status = ''): array
    {
        $builder = $this->db->table('tickets t')
            ->select('t.id, t.ticket_number, t.summary, t.status, t.priority,
                      t.created_at, t.resolved_at,
                      c.name as customer_name,
                      sc.name as category_name')
            ->join('customers c', 'c.id = t.customer_id', 'left')
            ->join('service_categories sc', 'sc.id = t.service_category_id', 'left')
            ->where('t.assigned_staff_id', $staffId);

        if (! empty($status)) {
            $builder->where('t.status', $status);
        }

        return $builder->orderBy('t.created_at', 'DESC')->limit(100)->get()->getResultArray();
    }

    public function emailExists(string $email, int $excludeId = 0): bool
    {
        $builder = $this->db->table('staff')->where('email', $email);
        if ($excludeId > 0) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }
}
