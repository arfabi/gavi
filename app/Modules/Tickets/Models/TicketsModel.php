<?php

namespace App\Modules\Tickets\Models;

use CodeIgniter\Model;

class TicketsModel extends Model
{
    protected $table      = 'tickets';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'ticket_number', 'customer_id', 'service_category_id',
        'assigned_staff_id', 'summary', 'catatan_petugas',
        'status', 'priority', 'resolved_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getList(array $filters, bool $isAdmin, int $staffId, int $perPage = 20): array
    {
        $builder = $this->db->table('tickets t')
            ->select('t.*, c.name as customer_name, c.whatsapp_number,
                      sc.name as category_name, s.name as assigned_name')
            ->join('customers c', 'c.id = t.customer_id', 'left')
            ->join('service_categories sc', 'sc.id = t.service_category_id', 'left')
            ->join('staff s', 's.id = t.assigned_staff_id', 'left');

        // Petugas hanya lihat tiket miliknya atau unassigned
        if (! $isAdmin) {
            $builder->groupStart()
                ->where('t.assigned_staff_id', $staffId)
                ->orWhere('t.assigned_staff_id IS NULL')
                ->groupEnd();
        }

        if (! empty($filters['status'])) {
            $builder->where('t.status', $filters['status']);
        }

        if (! empty($filters['priority'])) {
            $builder->where('t.priority', $filters['priority']);
        }

        if (! empty($filters['search'])) {
            $builder->groupStart()
                ->like('t.ticket_number', $filters['search'])
                ->orLike('c.name', $filters['search'])
                ->orLike('t.summary', $filters['search'])
                ->groupEnd();
        }

        $builder->orderBy('
            CASE t.priority WHEN "high" THEN 1 WHEN "medium" THEN 2 ELSE 3 END', '', false)
            ->orderBy('t.created_at', 'DESC');

        $total  = $builder->countAllResults(false);
        $page   = (int) ($filters['page'] ?? 1);
        $offset = ($page - 1) * $perPage;
        $rows   = $builder->limit($perPage, $offset)->get()->getResultArray();

        return ['rows' => $rows, 'total' => $total, 'perPage' => $perPage, 'page' => $page];
    }

    public function getDetail(int $id): ?array
    {
        return $this->db->table('tickets t')
            ->select('t.*,
                      c.name as customer_name, c.whatsapp_number, c.instansi,
                      c.nik, c.address, c.address_city, c.address_province,
                      c.ai_mode, c.last_interaction, c.created_at as customer_created_at,
                      sc.name as category_name, s.name as assigned_name')
            ->join('customers c', 'c.id = t.customer_id', 'left')
            ->join('service_categories sc', 'sc.id = t.service_category_id', 'left')
            ->join('staff s', 's.id = t.assigned_staff_id', 'left')
            ->where('t.id', $id)
            ->get()->getRowArray();
    }

    public function getChatLogs(int $ticketId): array
    {
        // Ambil chat_logs dari session customer yang terkait tiket
        $ticket = $this->find($ticketId);
        if (! $ticket) {
            return [];
        }

        return $this->db->table('chat_logs cl')
            ->select('cl.*, s.name as staff_name')
            ->join('staff s', 's.id = cl.staff_id', 'left')
            ->where('cl.customer_id', $ticket['customer_id'])
            ->orderBy('cl.created_at', 'ASC')
            ->limit(100)
            ->get()->getResultArray();
    }

    public function countByStatus(bool $isAdmin, int $staffId): array
    {
        $statuses = ['open', 'pending', 'resolved', 'closed'];
        $result   = [];

        foreach ($statuses as $status) {
            $builder = $this->db->table('tickets');
            if (! $isAdmin) {
                $builder->groupStart()
                    ->where('assigned_staff_id', $staffId)
                    ->orWhere('assigned_staff_id IS NULL')
                    ->groupEnd();
            }
            $result[$status] = $builder->where('status', $status)->countAllResults();
        }

        return $result;
    }

    public function assign(int $id, int $staffId): void
    {
        $this->update($id, [
            'assigned_staff_id' => $staffId,
            'status'            => 'pending',
        ]);
    }

    public function resolve(int $id, string $catatan): void
    {
        $this->update($id, [
            'status'          => 'resolved',
            'catatan_petugas' => $catatan,
            'resolved_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    public function insertReply(int $ticketId, int $staffId, string $message): array
    {
        $ticket = $this->find($ticketId);
        if (! $ticket) {
            return [];
        }

        // Cari session terbaru customer ini
        $session = $this->db->table('session')
            ->where('customer_id', $ticket['customer_id'])
            ->orderBy('create_at', 'DESC')
            ->limit(1)
            ->get()->getRowArray();

        $sessionId = $session['id'] ?? null;

        $this->db->table('chat_logs')->insert([
            'session_id'       => $sessionId,
            'customer_id'      => $ticket['customer_id'],
            'sender'           => 'staff',
            'message'          => $message,
            'staff_id'         => $staffId,
            'agent_handler'    => 'staff',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        $insertId = $this->db->insertID();

        return $this->db->table('chat_logs cl')
            ->select('cl.*, s.name as staff_name')
            ->join('staff s', 's.id = cl.staff_id', 'left')
            ->where('cl.id', $insertId)
            ->get()->getRowArray() ?: [];
    }

    public function getTemplates(int $categoryId): array
    {
        return $this->db->table('knowledge_base')
            ->select('id, judul, konten')
            ->where('service_category_id', $categoryId)
            ->where('aktif', 1)
            ->orderBy('judul')
            ->limit(30)
            ->get()->getResultArray();
    }

    public function searchTemplates(string $keyword, int $categoryId = 0): array
    {
        $builder = $this->db->table('knowledge_base kb')
            ->select('kb.id, kb.judul, kb.konten, sc.name as category_name')
            ->join('service_categories sc', 'sc.id = kb.service_category_id', 'left')
            ->where('kb.aktif', 1);

        if ($categoryId > 0) {
            $builder->where('kb.service_category_id', $categoryId);
        }

        if (! empty($keyword)) {
            $builder->groupStart()
                ->like('kb.judul', $keyword)
                ->orLike('kb.konten', $keyword)
                ->groupEnd();
        }

        return $builder->orderBy('kb.judul')->limit(30)->get()->getResultArray();
    }

    public function generateTicketNumber(): string
    {
        $date     = date('Ymd');
        $prefix   = 'TKT-' . $date . '-';
        $lastRow  = $this->db->table('tickets')
            ->like('ticket_number', $prefix, 'after')
            ->orderBy('ticket_number', 'DESC')
            ->limit(1)
            ->get()->getRowArray();

        $seq = 1;
        if ($lastRow) {
            $parts = explode('-', $lastRow['ticket_number']);
            $seq   = (int) end($parts) + 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
