<?php

namespace App\Modules\Staff\Models;

use CodeIgniter\Model;

class DivisionsModel extends Model
{
    protected $table      = 'divisions';
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'description'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getAll(): array
    {
        return $this->db->table('divisions d')
            ->select('d.*,
                      (SELECT COUNT(*) FROM staff WHERE division_id = d.id) as staff_count,
                      (SELECT COUNT(*) FROM service_categories WHERE divisions_id = d.id) as service_count')
            ->orderBy('d.name')
            ->get()->getResultArray();
    }

    public function nameExists(string $name, int $excludeId = 0): bool
    {
        $builder = $this->db->table('divisions')->where('name', $name);
        if ($excludeId > 0) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }
}
