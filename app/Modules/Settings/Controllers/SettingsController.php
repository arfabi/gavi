<?php

namespace App\Modules\Settings\Controllers;

use App\Modules\Settings\Models\SettingsModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class SettingsController extends Controller
{
    private SettingsModel $model;

    public function __construct()
    {
        $this->model = new SettingsModel();
    }

    public function index(): string
    {
        $settings = $this->model->getAllGrouped();

        return view('App\Views\layouts\main', [
            'title'      => 'Pengaturan Sistem',
            'pageTitle'  => 'Pengaturan Sistem',
            'breadcrumb' => [['label' => 'Pengaturan Sistem']],
            'content'    => view('App\Modules\Settings\Views\index', [
                'settings' => $settings,
                'activeTab' => service('request')->getGet('tab') ?? 'general',
            ]),
        ]);
    }

    public function save(): RedirectResponse
    {
        $group = $this->request->getPost('group');

        $allowed = ['General', 'RAG', 'N8N', 'WAHA', 'Supabase'];
        if (! in_array($group, $allowed)) {
            return redirect()->back()->with('error', 'Grup pengaturan tidak valid.');
        }

        $rows = $this->model->where('setting_group', $group)->findAll();
        $data = [];
        foreach ($rows as $row) {
            $posted = $this->request->getPost($row['setting_key']);
            // Checkbox (toggle) → tidak dikirim jika unchecked, default ke '0'
            $data[$row['setting_key']] = $posted !== null ? (string) $posted : '0';
        }

        $this->model->saveGroup($group, $data);

        $tab = strtolower($group);
        return redirect()->to('/settings?tab=' . $tab)
            ->with('success', 'Pengaturan ' . $group . ' berhasil disimpan.');
    }

    public function testConnection(): ResponseInterface
    {
        $target = $this->request->getPost('target');

        if ($target === 'waha') {
            return $this->testWaha();
        }

        if ($target === 'n8n') {
            return $this->testN8n();
        }

        return $this->response->setStatusCode(422)
            ->setJSON(['success' => false, 'message' => 'Target tidak dikenal.']);
    }

    private function testWaha(): ResponseInterface
    {
        $cfg = $this->model->getGroup('WAHA');
        $url     = rtrim($cfg['waha_endpoint_url'] ?? '', '/');
        $session = $cfg['waha_session_name'] ?? 'default';
        $apiKey  = $cfg['waha_api_key'] ?? '';

        if (empty($url)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Endpoint WAHA belum dikonfigurasi.']);
        }

        $client = \Config\Services::curlrequest();
        try {
            $res = $client->get($url . '/api/sessions/' . $session, [
                'headers' => ['X-Api-Key' => $apiKey],
                'timeout' => 8,
                'http_errors' => false,
            ]);

            $code = $res->getStatusCode();
            if ($code === 200 || $code === 201) {
                $body = json_decode($res->getBody(), true);
                $status = $body['status'] ?? 'unknown';
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'WAHA terhubung. Session status: <strong>' . esc($status) . '</strong>',
                    'csrf'    => csrf_hash(),
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'WAHA merespons HTTP ' . $code . '. Periksa URL dan API Key.',
                'csrf'    => csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal terhubung ke WAHA: ' . $e->getMessage(),
                'csrf'    => csrf_hash(),
            ]);
        }
    }

    private function testN8n(): ResponseInterface
    {
        $cfg     = $this->model->getGroup('N8N');
        $webhook = $cfg['n8n_webhook_url'] ?? '';
        $token   = $cfg['n8n_api_token'] ?? '';

        if (empty($webhook)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Webhook URL N8N belum dikonfigurasi.']);
        }

        $client = \Config\Services::curlrequest();
        try {
            $res = $client->get($webhook, [
                'headers'     => ['Authorization' => 'Bearer ' . $token],
                'timeout'     => 8,
                'http_errors' => false,
            ]);

            $code = $res->getStatusCode();
            if ($code >= 200 && $code < 500) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'N8N Webhook dapat dijangkau (HTTP ' . $code . ').',
                    'csrf'    => csrf_hash(),
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'N8N merespons HTTP ' . $code . '.',
                'csrf'    => csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal terhubung ke N8N: ' . $e->getMessage(),
                'csrf'    => csrf_hash(),
            ]);
        }
    }
}
