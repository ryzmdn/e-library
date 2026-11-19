<?php
declare(strict_types=1);

namespace App\Controllers\Member;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\FineLedgerModel;
use App\Models\LoanTransactionModel;
use App\Models\ReservationQueueModel;
use App\Models\UserModel;
use Throwable;

final class DashboardMemberController extends Controller
{
    private UserModel $userModel;
    private LoanTransactionModel $loanModel;
    private FineLedgerModel $fineModel;
    private ReservationQueueModel $reservationModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->loanModel = new LoanTransactionModel();
        $this->fineModel = new FineLedgerModel();
        $this->reservationModel = new ReservationQueueModel();
    }

    public function index(Request $request): Response
    {
        $sessionUser = Session::getUser();
        $userId = (int)($sessionUser['id'] ?? 0);

        $user = $this->userModel->findById($userId);
        if (!$user) {
            Session::destroy();
            return $this->redirect('/login');
        }

        $activeLoans = [];
        $overdueCount = 0;
        $unpaidFine = 0.00;
        $activeReservations = [];
        $recentHistory = [];

        try {
            $activeLoans = $this->loanModel->getActiveLoansByUserId($userId);
            $overdueCount = $this->loanModel->countOverdueLoansByUserId($userId);
            $unpaidFine = $this->fineModel->getTotalUnpaidFine($userId);
            $activeReservations = $this->reservationModel->getActiveReservationsByUserId($userId);
            $recentHistory = $this->loanModel->getRecentHistoryByUserId($userId, 5);
        } catch (Throwable $e) {
            $activeLoans = [];
        }

        return $this->render('pages/member/dashboard', [
            'pageTitle' => 'Dasbor Anggota',
            'member' => $user,
            'activeLoans' => $activeLoans,
            'activeLoansCount' => count($activeLoans),
            'overdueCount' => $overdueCount,
            'unpaidFine' => $unpaidFine,
            'activeReservations' => $activeReservations,
            'activeReservationsCount' => count($activeReservations),
            'recentHistory' => $recentHistory,
        ]);
    }

    public function profile(Request $request): Response
    {
        $sessionUser = Session::getUser();
        $userId = (int)($sessionUser['id'] ?? 0);

        $user = $this->userModel->findById($userId);
        if (!$user) {
            return $this->redirect('/login');
        }

        return $this->render('pages/member/profile', [
            'pageTitle' => 'Pengaturan Profil & Preferensi',
            'member' => $user,
        ]);
    }

    public function updateProfile(Request $request): Response
    {
        $sessionUser = Session::getUser();
        $userId = (int)($sessionUser['id'] ?? 0);

        $user = $this->userModel->findById($userId);
        if (!$user) {
            return $this->redirect('/login');
        }

        $fullName = trim((string)$request->input('full_name', ''));
        $phone = trim((string)$request->input('phone_number', ''));

        $validator = Validator::make([
            'full_name' => $fullName,
        ], [
            'full_name' => 'required|min:3|max:150',
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->getFirstError());
            return $this->redirect('/member/profile');
        }

        $this->userModel->updateProfile($userId, [
            'full_name' => $fullName,
            'phone_number' => $phone !== '' ? $phone : null,
            'theme_preference' => 'LIGHT',
        ]);

        $currentPassword = (string)$request->input('current_password', '');
        $newPassword = (string)$request->input('new_password', '');
        $newPasswordConfirmation = (string)$request->input('new_password_confirmation', '');

        if ($currentPassword !== '' || $newPassword !== '') {
            $passValidator = Validator::make([
                'current_password' => $currentPassword,
                'new_password' => $newPassword,
                'new_password_confirmation' => $newPasswordConfirmation,
            ], [
                'current_password' => 'required',
                'new_password' => 'required|min:6|max:100',
                'new_password_confirmation' => 'required|matches:new_password',
            ]);

            if ($passValidator->fails()) {
                Session::setFlash('error', $passValidator->getFirstError());
                return $this->redirect('/member/profile');
            }

            if (!password_verify($currentPassword, $user['password_hash'])) {
                Session::setFlash('error', 'Kata sandi saat ini yang Anda masukkan salah.');
                return $this->redirect('/member/profile');
            }

            $newHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
            $this->userModel->updatePassword($userId, $newHash);
        }

        $refreshed = $this->userModel->findById($userId);
        if ($refreshed) {
            Session::setUser($refreshed);
        }

        Session::setFlash('success', 'Perubahan profil dan preferensi berhasil disimpan.');
        return $this->redirect('/member/profile');
    }
}
