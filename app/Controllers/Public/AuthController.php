<?php
declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\UserModel;
use Throwable;

final class AuthController extends Controller
{
    private ?UserModel $userModel;

    public function __construct()
    {
        try {
            $this->userModel = new UserModel();
        } catch (Throwable $e) {
            $this->userModel = null;
        }
    }

    public function showLoginForm(Request $request): Response
    {
        return $this->render('pages/auth/login', [
            'pageTitle' => 'Masuk ke Akun',
            'oldIdentity' => Session::getFlash('old_identity')[0] ?? '',
        ], 'auth');
    }

    public function login(Request $request): Response
    {
        $identity = trim((string)$request->input('identity', ''));
        $password = (string)$request->input('password', '');

        $validator = Validator::make([
            'identity' => $identity,
            'password' => $password,
        ], [
            'identity' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->getFirstError());
            Session::setFlash('old_identity', $identity);
            return $this->redirect('/login');
        }

        if ($this->userModel === null) {
            Session::setFlash('error', 'Layanan autentikasi basis data sedang tidak dapat dihubungi.');
            Session::setFlash('old_identity', $identity);
            return $this->redirect('/login');
        }

        $user = $this->userModel->findByIdentifier($identity);

        if (!$user) {
            Session::setFlash('error', 'Kombinasi email atau nomor anggota dan kata sandi tidak ditemukan.');
            Session::setFlash('old_identity', $identity);
            return $this->redirect('/login');
        }

        if (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
            $remainingMinutes = ceil((strtotime($user['locked_until']) - time()) / 60);
            Session::setFlash('error', "Akun Anda sementara terkunci karena beberapa kali percobaan gagal. Silakan coba kembali dalam {$remainingMinutes} menit.");
            return $this->redirect('/login');
        }

        if ($user['account_status'] === 'SUSPENDED') {
            Session::setFlash('error', 'Akun Anda sedang dibekukan oleh petugas perpustakaan. Silakan hubungi bagian sirkulasi.');
            return $this->redirect('/login');
        }

        if ($user['account_status'] === 'PENDING_VERIFICATION') {
            Session::setFlash('error', 'Akun Anda sedang menunggu verifikasi administrasi keanggotaan.');
            return $this->redirect('/login');
        }

        if (!password_verify($password, $user['password_hash'])) {
            $this->userModel->recordFailedLogin((int)$user['id']);
            Session::setFlash('error', 'Kombinasi email atau nomor anggota dan kata sandi tidak ditemukan.');
            Session::setFlash('old_identity', $identity);
            return $this->redirect('/login');
        }

        $this->userModel->resetFailedLogins((int)$user['id']);

        Session::setUser($user);

        if (in_array($user['role'], ['SUPER_ADMIN', 'LIBRARIAN', 'CATALOGER'], true)) {
            return $this->redirect('/admin/dashboard');
        }

        return $this->redirect('/member/dashboard');
    }

    public function showRegisterForm(Request $request): Response
    {
        return $this->render('pages/auth/register', [
            'pageTitle' => 'Pendaftaran Anggota Baru',
            'oldName' => Session::getFlash('old_name')[0] ?? '',
            'oldEmail' => Session::getFlash('old_email')[0] ?? '',
            'oldPhone' => Session::getFlash('old_phone')[0] ?? '',
        ], 'auth');
    }

    public function register(Request $request): Response
    {
        $fullName = trim((string)$request->input('full_name', ''));
        $email = trim((string)$request->input('email', ''));
        $phone = trim((string)$request->input('phone_number', ''));
        $password = (string)$request->input('password', '');
        $passwordConfirmation = (string)$request->input('password_confirmation', '');

        $validator = Validator::make([
            'full_name' => $fullName,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'full_name' => 'required|min:3|max:150',
            'email' => 'required|email|max:190',
            'password' => 'required|min:6|max:100',
            'password_confirmation' => 'required|matches:password',
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->getFirstError());
            Session::setFlash('old_name', $fullName);
            Session::setFlash('old_email', $email);
            Session::setFlash('old_phone', $phone);
            return $this->redirect('/register');
        }

        if ($this->userModel === null) {
            Session::setFlash('error', 'Layanan pendaftaran basis data sedang tidak dapat dihubungi.');
            Session::setFlash('old_name', $fullName);
            Session::setFlash('old_email', $email);
            Session::setFlash('old_phone', $phone);
            return $this->redirect('/register');
        }

        if ($this->userModel->isEmailTaken($email)) {
            Session::setFlash('error', 'Alamat email ini sudah terdaftar dalam sistem.');
            Session::setFlash('old_name', $fullName);
            Session::setFlash('old_email', $email);
            Session::setFlash('old_phone', $phone);
            return $this->redirect('/register');
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $newUserId = $this->userModel->createMember([
            'full_name' => $fullName,
            'email' => $email,
            'phone_number' => $phone,
            'password_hash' => $passwordHash,
            'tier_id' => 1,
        ]);

        $user = $this->userModel->findById($newUserId);
        if ($user) {
            Session::setUser($user);
            Session::setFlash('success', "Pendaftaran berhasil. Nomor anggota Anda adalah {$user['member_number']}.");
            return $this->redirect('/member/dashboard');
        }

        Session::setFlash('success', 'Pendaftaran akun berhasil. Silakan masuk menggunakan email dan kata sandi Anda.');
        return $this->redirect('/login');
    }

    public function logout(Request $request): Response
    {
        Session::destroy();
        Session::start();
        Session::setFlash('info', 'Anda telah berhasil keluar dari akun.');
        return $this->redirect('/login');
    }
}
