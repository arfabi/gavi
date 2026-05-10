<?php

namespace App\Modules\Customers\Models;

use CodeIgniter\Model;

class CustomersModel extends Model
{
    protected $table      = 'customers';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'whatsapp_number', 'name', 'nik', 'instansi',
        'address', 'address_city', 'address_province', 'ai_mode',
    ];

    protected $useTimestamps = false;

    public function getList(array $filters, int $perPage = 20): array
    {
        $builder = $this->db->table('customers c')
            ->select('c.*,
                (SELECT COUNT(*) FROM session s WHERE s.customer_id = c.id) as total_sessions,
                (SELECT COUNT(*) FROM tickets t WHERE t.customer_id = c.id) as total_tickets,
                (SELECT COUNT(*) FROM chat_logs cl WHERE cl.customer_id = c.id) as total_chats')
            ->orderBy('c.last_interaction', 'DESC');

        if (! empty($filters['search'])) {
            $builder->groupStart()
                ->like('c.name', $filters['search'])
                ->orLike('c.whatsapp_number', $filters['search'])
                ->orLike('c.instansi', $filters['search'])
                ->orLike('c.nik', $filters['search'])
                ->groupEnd();
        }

        if (isset($filters['ai_mode']) && $filters['ai_mode'] !== '') {
            $builder->where('c.ai_mode', (int) $filters['ai_mode']);
        }

        $total  = $builder->countAllResults(false);
        $page   = max(1, (int) ($filters['page'] ?? 1));
        $offset = ($page - 1) * $perPage;
        $rows   = $builder->limit($perPage, $offset)->get()->getResultArray();

        return ['rows' => $rows, 'total' => $total, 'perPage' => $perPage, 'page' => $page];
    }

    public function getDetail(int $id): ?array
    {
        return $this->db->table('customers')
            ->where('id', $id)
            ->get()->getRowArray();
    }

    public function getSessions(int $customerId): array
    {
        return $this->db->table('session s')
            ->select('s.*,
                (SELECT COUNT(*) FROM chat_logs cl WHERE cl.session_id = s.id) as chat_count')
            ->where('s.customer_id', $customerId)
            ->orderBy('s.create_at', 'DESC')
            ->get()->getResultArray();
    }

    public function getChatLogs(int $customerId, ?int $sessionId = null): array
    {
        $builder = $this->db->table('chat_logs cl')
            ->select('cl.*, s2.name as staff_name')
            ->join('staff s2', 's2.id = cl.staff_id', 'left')
            ->where('cl.customer_id', $customerId)
            ->orderBy('cl.created_at', 'ASC');

        if ($sessionId !== null) {
            $builder->where('cl.session_id', $sessionId);
        }

        return $builder->limit(200)->get()->getResultArray();
    }

    public function getTickets(int $customerId): array
    {
        return $this->db->table('tickets t')
            ->select('t.*, sc.name as category_name, s.name as assigned_name')
            ->join('service_categories sc', 'sc.id = t.service_category_id', 'left')
            ->join('staff s', 's.id = t.assigned_staff_id', 'left')
            ->where('t.customer_id', $customerId)
            ->orderBy('t.created_at', 'DESC')
            ->get()->getResultArray();
    }

    public function getStats(): array
    {
        return [
            'total'        => $this->db->table('customers')->countAllResults(),
            'ai_active'    => $this->db->table('customers')->where('ai_mode', 1)->countAllResults(),
            'with_instansi'=> $this->db->table('customers')->where('instansi IS NOT NULL')->where('instansi !=', '')->countAllResults(),
            'new_today'    => $this->db->table('customers')
                ->where('DATE(created_at)', date('Y-m-d'))
                ->countAllResults(),
        ];
    }
}
