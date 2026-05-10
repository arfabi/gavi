<?php

namespace App\Modules\Staff\Models;

use CodeIgniter\Model;

class ServicesModel extends Model
{
    protected $table      = 'service_categories';
    protected $primaryKey = 'id';

    protected $allowedFields = ['divisions_id', 'name'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getAll(): array
    {
        return $this->db->table('service_categories sc')
            ->select('sc.*, d.name as division_name')
            ->join('divisions d', 'd.id = sc.divisions_id', 'left')
            ->orderBy('d.name, sc.name')
            ->get()->getResultArray();
    }

    public function getByDivision(int $divisionId): array
    {
        return $this->db->table('service_categories')
            ->where('divisions_id', $divisionId)
            ->orderBy('name')
            ->get()->getResultArray();
    }
}
