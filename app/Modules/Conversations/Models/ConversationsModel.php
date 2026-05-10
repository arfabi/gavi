<?php

namespace App\Modules\Conversations\Models;

use CodeIgniter\Model;

class ConversationsModel extends Model
{
    protected $table      = 'customers';
    protected $primaryKey = 'id';

    // ---------------------------------------------------------------
    // Customer list: last message + unread count
    // ---------------------------------------------------------------
    public function getCustomerList(string $search = ''): array
    {
        $builder = $this->db->table('customers c')
            ->select('c.id, c.name, c.whatsapp_number, c.ai_mode, c.last_interaction,
                      (SELECT cl.message FROM chat_logs cl
                       WHERE cl.customer_id = c.id
                       ORDER BY cl.created_at DESC LIMIT 1) AS last_message,
                      (SELECT cl.sender FROM chat_logs cl
                       WHERE cl.customer_id = c.id
                       ORDER BY cl.created_at DESC LIMIT 1) AS last_sender,
                      (SELECT cl.created_at FROM chat_logs cl
                       WHERE cl.customer_id = c.id
                       ORDER BY cl.created_at DESC LIMIT 1) AS last_message_at,
                      (SELECT COUNT(*) FROM chat_logs cl
                       WHERE cl.customer_id = c.id
                         AND cl.sender = "customer"
                         AND cl.created_at > IFNULL(
                             (SELECT MAX(cl2.created_at) FROM chat_logs cl2
                              WHERE cl2.customer_id = c.id AND cl2.sender IN ("staff","ai")),
                             "2000-01-01")) AS unread_count')
            ->orderBy('last_message_at', 'DESC');

        if (! empty($search)) {
            $builder->groupStart()
                ->like('c.name', $search)
                ->orLike('c.whatsapp_number', $search)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    // ---------------------------------------------------------------
    // Chat history for one customer
    // ---------------------------------------------------------------
    public function getChatHistory(int $customerId, int $limit = 100): array
    {
        return $this->db->table('chat_logs cl')
            ->select('cl.*, s.name as staff_name')
            ->join('staff s', 's.id = cl.staff_id', 'left')
            ->where('cl.customer_id', $customerId)
            ->orderBy('cl.created_at', 'ASC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    // ---------------------------------------------------------------
    // Polling: only messages after a given ID
    // ---------------------------------------------------------------
    public function getNewMessages(int $customerId, int $afterId): array
    {
        return $this->db->table('chat_logs cl')
            ->select('cl.*, s.name as staff_name')
            ->join('staff s', 's.id = cl.staff_id', 'left')
            ->where('cl.customer_id', $customerId)
            ->where('cl.id >', $afterId)
            ->orderBy('cl.created_at', 'ASC')
            ->get()->getResultArray();
    }

    // ---------------------------------------------------------------
    // Insert staff reply to chat_logs
    // ---------------------------------------------------------------
    public function insertReply(int $customerId, int $staffId, string $message): array
    {
        // Use latest session if exists
        $session = $this->db->table('session')
            ->where('customer_id', $customerId)
            ->orderBy('create_at', 'DESC')
            ->limit(1)
            ->get()->getRowArray();

        $this->db->table('chat_logs')->insert([
            'session_id'    => $session['id'] ?? null,
            'customer_id'   => $customerId,
            'sender'        => 'staff',
            'message'       => $message,
            'staff_id'      => $staffId,
            'agent_handler' => 'staff',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        $insertId = $this->db->insertID();

        return $this->db->table('chat_logs cl')
            ->select('cl.*, s.name as staff_name')
            ->join('staff s', 's.id = cl.staff_id', 'left')
            ->where('cl.id', $insertId)
            ->get()->getRowArray() ?: [];
    }

    // ---------------------------------------------------------------
    // Toggle AI mode for a customer
    // ---------------------------------------------------------------
    public function setAiMode(int $customerId, int $mode): void
    {
        $this->db->table('customers')->where('id', $customerId)->update(['ai_mode' => $mode]);
    }

    // ---------------------------------------------------------------
    // Single customer with full profile
    // ---------------------------------------------------------------
    public function getCustomer(int $id): ?array
    {
        return $this->db->table('customers')->where('id', $id)->get()->getRowArray() ?: null;
    }

    // ---------------------------------------------------------------
    // Templates (knowledge_base)
    // ---------------------------------------------------------------
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
}
