<?php

namespace App\Modules\Conversations\Controllers;

use App\Modules\Conversations\Models\ConversationsModel;
use App\Modules\Settings\Models\SettingsModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class ConversationsController extends Controller
{
    private ConversationsModel $model;
    private int $staffId;
    private bool $isAdmin;

    public function __construct()
    {
        $this->model   = new ConversationsModel();
        $this->staffId = (int) session()->get('staff_id');
        $this->isAdmin = session()->get('role') === 'admin';
    }

    // ----------------------------------------------------------------
    // Main page: layout with left customer list
    // ----------------------------------------------------------------
    public function index(): string
    {
        $search    = $this->request->getGet('search') ?? '';
        $customers = $this->model->getCustomerList($search);

        return view('App\Views\layouts\main', [
            'title'      => 'Percakapan',
            'pageTitle'  => 'Percakapan AI-Powered',
            'bodyClass'  => 'sidebar-collapse',
            'breadcrumb' => [['label' => 'Percakapan']],
            'content'    => view('App\Modules\Conversations\Views\index', [
                'customers' => $customers,
                'search'    => $search,
                'isAdmin'   => $this->isAdmin,
            ]),
        ]);
    }

    // ----------------------------------------------------------------
    // Right panel (HTML partial) — loaded via AJAX
    // ----------------------------------------------------------------
    public function chat(int $customerId): string
    {
        $customer = $this->model->getCustomer($customerId);
        if (! $customer) {
            return '<div class="p-4 text-danger">Customer tidak ditemukan.</div>';
        }

        $chats     = $this->model->getChatHistory($customerId);
        $templates = $this->model->searchTemplates('');

        return view('App\Modules\Conversations\Views\chat', [
            'customer'  => $customer,
            'chats'     => $chats,
            'templates' => $templates,
            'staffId'   => $this->staffId,
            'isAdmin'   => $this->isAdmin,
        ]);
    }

    // ----------------------------------------------------------------
    // Send reply (AJAX POST)
    // ----------------------------------------------------------------
    public function reply(int $customerId): ResponseInterface
    {
        $customer = $this->model->getCustomer($customerId);
        if (! $customer) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Customer tidak ditemukan.']);
        }

        $message = trim($this->request->getPost('message') ?? '');
        if (empty($message)) {
            return $this->response->setStatusCode(422)
                ->setJSON(['success' => false, 'message' => 'Pesan tidak boleh kosong.']);
        }

        $chat = $this->model->insertReply($customerId, $this->staffId, $message);

        // Try to send via WAHA (non-blocking; fail silently)
        $this->sendViaWaha($customer['whatsapp_number'], $message);

        return $this->response->setJSON([
            'success'    => true,
            'chat'       => $chat,
            'staff_name' => session()->get('name'),
            'csrf'       => csrf_hash(),
        ]);
    }

    // ----------------------------------------------------------------
    // Takeover — disable AI for this customer
    // ----------------------------------------------------------------
    public function takeover(int $customerId): ResponseInterface
    {
        $customer = $this->model->getCustomer($customerId);
        if (! $customer) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Customer tidak ditemukan.']);
        }

        $this->model->setAiMode($customerId, 0);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Percakapan berhasil diambil alih. AI dinonaktifkan untuk customer ini.',
            'ai_mode' => 0,
            'csrf'    => csrf_hash(),
        ]);
    }

    // ----------------------------------------------------------------
    // Release — re-enable AI for this customer
    // ----------------------------------------------------------------
    public function release(int $customerId): ResponseInterface
    {
        $customer = $this->model->getCustomer($customerId);
        if (! $customer) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Customer tidak ditemukan.']);
        }

        $this->model->setAiMode($customerId, 1);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Percakapan dikembalikan ke AI.',
            'ai_mode' => 1,
            'csrf'    => csrf_hash(),
        ]);
    }

    // ----------------------------------------------------------------
    // Poll — new messages after a given ID
    // ----------------------------------------------------------------
    public function poll(int $customerId): ResponseInterface
    {
        $afterId  = (int) ($this->request->getGet('after_id') ?? 0);
        $customer = $this->model->getCustomer($customerId);

        if (! $customer) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false]);
        }

        $messages = $this->model->getNewMessages($customerId, $afterId);

        return $this->response->setJSON([
            'success'  => true,
            'messages' => $messages,
            'ai_mode'  => (int) $customer['ai_mode'],
            'csrf'     => csrf_hash(),
        ]);
    }

    // ----------------------------------------------------------------
    // Template search (AJAX)
    // ----------------------------------------------------------------
    public function templates(): ResponseInterface
    {
        $keyword = trim($this->request->getGet('q') ?? '');
        $results = $this->model->searchTemplates($keyword);

        return $this->response->setJSON([
            'success'   => true,
            'templates' => $results,
        ]);
    }

    // ----------------------------------------------------------------
    // Internal: send message via WAHA API
    // ----------------------------------------------------------------
    private function sendViaWaha(string $waNumber, string $message): void
    {
        try {
            $settings = new SettingsModel();
            $cfg      = $settings->getGroup('WAHA');
            $endpoint = rtrim($cfg['waha_endpoint_url'] ?? '', '/');
            $session  = $cfg['waha_session_name'] ?? 'default';
            $apiKey   = $cfg['waha_api_key'] ?? '';

            if (empty($endpoint)) {
                return;
            }

            // Convert +6281xxx → 6281xxx@c.us
            $chatId = ltrim(preg_replace('/[^0-9]/', '', $waNumber), '0');
            // Keep leading country code digits — just strip non-numeric and leading +
            $chatId = preg_replace('/^\+/', '', $waNumber);
            $chatId = preg_replace('/[^0-9]/', '', $chatId) . '@c.us';

            $client = \Config\Services::curlrequest();
            $client->post($endpoint . '/api/sendText', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Api-Key'    => $apiKey,
                ],
                'json'        => ['session' => $session, 'chatId' => $chatId, 'text' => $message],
                'timeout'     => 5,
                'http_errors' => false,
            ]);
        } catch (\Throwable) {
            // Silent fail — message is already in chat_logs
        }
    }
}
