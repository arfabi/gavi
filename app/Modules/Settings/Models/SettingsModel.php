<?php

namespace App\Modules\Settings\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table      = 'settings';
    protected $primaryKey = 'id';

    protected $allowedFields = ['setting_group', 'setting_key', 'setting_value', 'description'];

    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'updated_at';

    public function getAllGrouped(): array
    {
        $rows   = $this->findAll();
        $groups = [];
        foreach ($rows as $row) {
            $groups[$row['setting_group']][$row['setting_key']] = $row;
        }
        return $groups;
    }

    public function getGroup(string $group): array
    {
        $rows   = $this->where('setting_group', $group)->findAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['setting_key']] = $row['setting_value'];
        }
        return $result;
    }

    public function setValue(string $key, string $value): void
    {
        $this->where('setting_key', $key)->set('setting_value', $value)->update();
    }

    public function saveGroup(string $group, array $data): void
    {
        $rows = $this->where('setting_group', $group)->findAll();
        foreach ($rows as $row) {
            $key = $row['setting_key'];
            if (array_key_exists($key, $data)) {
                $this->update($row['id'], ['setting_value' => (string) $data[$key]]);
            }
        }
    }
}
