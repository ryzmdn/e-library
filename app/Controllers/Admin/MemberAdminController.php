<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\AuditLogModel;
use App\Models\FineLedgerModel;
use App\Models\LoanTransactionModel;
use App\Models\UserModel;
use Throwable;

final class MemberAdminController extends Controller
{
    private UserModel $userModel;
    private LoanTransactionModel $loanModel;
    private FineLedgerModel $fineModel;
    private AuditLogModel $auditModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->loanModel = new LoanTransactionModel();
        $this->fineModel = new FineLedgerModel();
        $this->auditModel = new AuditLogModel();
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int)($request->get('page') ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $filters = [
            'keyword' => $request->get('q') ?? '',
            'role' => $request->get('role') ?? '',
            'status' => $request->get('status') ?? '',
            'tier_id' => $request->get('tier_id') ?? '',
        ];

        $users = $this->userModel->getAllUsers($filters, $limit, $offset);
        $totalUsers = $this->userModel->countAllUsers($filters);
        $totalPages = (int)ceil($totalUsers / $limit);

        return $this->render('pages/admin/members/index', [
            'pageTitle' => 'Manajemen Data Anggota',
            'users' => $users,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
        ], 'admin');
    }

    public function detail(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $member = $this->userModel->findById($id);
        if ($member === null) {
            Session::flash('error', 'Data pengguna tidak ditemukan.');
            return $this->redirect('/admin/members');
        }

        $activeLoans = [];
        $unpaidBalance = 0.00;
        $fines = [];

        try {
            $activeLoans = $this->loanModel->getActiveLoansByUserId($id);
            $unpaidBalance = $this->fineModel->getTotalUnpaidFine($id);
            $fines = $this->fineModel->getLedgerByUserId($id, 10);
        } catch (Throwable $e) {
        }

        return $this->render('pages/admin/members/detail', [
            'pageTitle' => 'Detail Anggota: ' . $member['full_name'],
            'member' => $member,
            'activeLoans' => $activeLoans,
            'unpaidBalance' => $unpaidBalance,
            'fines' => $fines,
        ], 'admin');
    }

    public function updateStatus(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $member = $this->userModel->findById($id);
        if ($member === null) {
            Session::flash('error', 'Data pengguna tidak ditemukan.');
            return $this->redirect('/admin/members');
        }

        $status = trim((string)$request->post('account_status'));
        $success = $this->userModel->updateAccountStatus($id, $status);

        if ($success) {
            $staffUser = Session::get('user');
            $staffId = $staffUser ? (int)$staffUser['id'] : null;
            $this->auditModel->log($staffId, 'UPDATE_USER_STATUS', 'users', $id, [
                'from' => $member['account_status'],
                'to' => $status
            ]);
            Session::flash('success', 'Status akun pengguna berhasil diubah menjadi ' . $status . '.');
        } else {
            Session::flash('error', 'Gagal memperbarui status akun pengguna.');
        }

        return $this->redirect('/admin/members/' . $id);
    }

    public function updateTier(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $tierId = (int)$request->post('tier_id');

        $success = $this->userModel->updateTier($id, $tierId);
        if ($success) {
            $staffUser = Session::get('user');
            $staffId = $staffUser ? (int)$staffUser['id'] : null;
            $this->auditModel->log($staffId, 'UPDATE_USER_TIER', 'users', $id, ['tier_id' => $tierId]);
            Session::flash('success', 'Tingkat keanggotaan pengguna berhasil diperbarui.');
        } else {
            Session::flash('error', 'Gagal memperbarui tingkat keanggotaan.');
        }

        return $this->redirect('/admin/members/' . $id);
    }

    public function extendMembership(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $months = max(1, (int)($request->post('months') ?: 12));

        $success = $this->userModel->extendMembership($id, $months);
        if ($success) {
            $staffUser = Session::get('user');
            $staffId = $staffUser ? (int)$staffUser['id'] : null;
            $this->auditModel->log($staffId, 'EXTEND_MEMBERSHIP', 'users', $id, ['months_added' => $months]);
            Session::flash('success', 'Masa aktif keanggotaan berhasil diperpanjang ' . $months . ' bulan.');
        } else {
            Session::flash('error', 'Gagal memperpanjang masa aktif keanggotaan.');
        }

        return $this->redirect('/admin/members/' . $id);
    }
}
