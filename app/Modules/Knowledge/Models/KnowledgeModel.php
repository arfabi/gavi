<?php

namespace App\Modules\Knowledge\Models;

use CodeIgniter\Model;

class KnowledgeModel extends Model
{
    protected $table      = 'knowledge_base';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'service_category_id', 'judul', 'konten',
        'aktif', 'synced_to_supabase', 'supabase_id', 'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getWithCategory(array $filters = [], int $perPage = 20): array
    {
        $builder = $this->db->table('knowledge_base kb')
            ->select('kb.*, sc.name as category_name, s.name as creator_name')
            ->join('service_categories sc', 'sc.id = kb.service_category_id', 'left')
            ->join('staff s', 's.id = kb.created_by', 'left');

        if (! empty($filters['search'])) {
            $builder->groupStart()
                ->like('kb.judul', $filters['search'])
                ->orLike('kb.konten', $filters['search'])
                ->groupEnd();
        }

        if (isset($filters['category_id']) && $filters['category_id'] !== '') {
            $builder->where('kb.service_category_id', $filters['category_id']);
        }

        if (isset($filters['aktif']) && $filters['aktif'] !== '') {
            $builder->where('kb.aktif', $filters['aktif']);
        }

        if (isset($filters['synced']) && $filters['synced'] !== '') {
            $builder->where('kb.synced_to_supabase', $filters['synced']);
        }

        $builder->orderBy('kb.updated_at', 'DESC');

        $total = $builder->countAllResults(false);
        $page  = (int) ($filters['page'] ?? 1);
        $offset = ($page - 1) * $perPage;

        $rows = $builder->limit($perPage, $offset)->get()->getResultArray();

        return ['rows' => $rows, 'total' => $total, 'perPage' => $perPage, 'page' => $page];
    }

    public function getOneWithCategory(int $id): ?array
    {
        return $this->db->table('knowledge_base kb')
            ->select('kb.*, sc.name as category_name')
            ->join('service_categories sc', 'sc.id = kb.service_category_id', 'left')
            ->where('kb.id', $id)
            ->get()->getRowArray();
    }

    public function markSynced(int $id, string $supabaseId = ''): void
    {
        $this->update($id, [
            'synced_to_supabase' => 1,
            'supabase_id'        => $supabaseId,
        ]);
    }

    public function markUnsynced(int $id): void
    {
        $this->update($id, ['synced_to_supabase' => 0, 'supabase_id' => null]);
    }

    public function getStats(): array
    {
        $row = $this->db->query("SELECT
            COUNT(*) as total,
            SUM(aktif = 1) as aktif,
            SUM(synced_to_supabase = 1) as synced,
            SUM(synced_to_supabase = 0) as unsynced
            FROM knowledge_base")->getRowArray();
        return $row ?? ['total' => 0, 'aktif' => 0, 'synced' => 0, 'unsynced' => 0];
    }
}
