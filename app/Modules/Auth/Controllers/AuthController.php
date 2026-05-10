<?php

namespace App\Modules\Auth\Controllers;

use App\Libraries\Captcha;
use App\Modules\Auth\Models\StaffModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;

class AuthController extends Controller
{
    private StaffModel $staffModel;
    private Captcha $captcha;

    private const MAX_ATTEMPTS  = 5;
    private const LOCKOUT_SECS  = 900; // 15 menit

    public function __construct()
    {
        $this->staffModel = new StaffModel();
        $this->captcha    = new Captcha();
    }

    public function login(): string|RedirectResponse
    {
        if (session()->get('staff_id')) {
            return $this->redirectByRole();
        }

        $captchaHtml = $this->captcha->generate();

        return view('App\Modules\Auth\Views\login', [
            'captchaHtml' => $captchaHtml,
            'title'       => 'Login - GAVI Dashboard',
        ]);
    }

    public function loginPost(): RedirectResponse
    {
        $session = session();

        // Cek lockout
        if ($this->isLockedOut()) {
            $remaining = $this->lockoutRemaining();
            return redirect()->back()
                ->with('error', "Terlalu banyak percobaan gagal. Coba lagi dalam {$remaining} menit.");
        }

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'captcha'  => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Verifikasi CAPTCHA
        if (! $this->captcha->verify($this->request->getPost('captcha'))) {
            $this->incrementAttempts();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Kode CAPTCHA salah.');
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $staff    = $this->staffModel->findByEmail($email);

        if (! $staff || ! password_verify($password, $staff['password'])) {
            $this->incrementAttempts();
            $attempts = $session->get('login_attempts') ?? 0;
            $remaining = self::MAX_ATTEMPTS - $attempts;

            return redirect()->back()
                ->withInput()
                ->with('error', "Email atau password salah. Sisa percobaan: {$remaining}");
        }

        // Login sukses - reset attempts
        $session->remove('login_attempts');
        $session->remove('login_locked_until');

        $session->set([
            'staff_id'    => $staff['id'],
            'name'        => $staff['name'],
            'email'       => $staff['email'],
            'role'        => $staff['role'],
            'division_id' => $staff['division_id'],
        ]);

        $this->staffModel->updateLastLogin($staff['id']);

        return $this->redirectByRole();
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'Berhasil logout.');
    }

    public function captchaImage(): string
    {
        // Endpoint untuk refresh CAPTCHA via AJAX
        return $this->captcha->generate();
    }

    private function redirectByRole(): RedirectResponse
    {
        $role = session()->get('role');
        return $role === 'admin'
            ? redirect()->to('/dashboard')
            : redirect()->to('/tickets');
    }

    private function isLockedOut(): bool
    {
        $lockedUntil = session()->get('login_locked_until');
        if ($lockedUntil && time() < $lockedUntil) {
            return true;
        }
        return false;
    }

    private function lockoutRemaining(): int
    {
        $lockedUntil = session()->get('login_locked_until') ?? 0;
        return (int) ceil(($lockedUntil - time()) / 60);
    }

    private function incrementAttempts(): void
    {
        $session  = session();
        $attempts = ($session->get('login_attempts') ?? 0) + 1;
        $session->set('login_attempts', $attempts);

        if ($attempts >= self::MAX_ATTEMPTS) {
            $session->set('login_locked_until', time() + self::LOCKOUT_SECS);
            $session->set('login_attempts', 0);
        }
    }
}
