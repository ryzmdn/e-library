<?php
declare(strict_types=1);

namespace App\Controllers\Member;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\LoanTransactionModel;
use App\Models\UserModel;
use App\Services\CirculationService;
use Throwable;

final class LoanMemberController extends Controller
{
    private LoanTransactionModel $loanModel;
    private CirculationService $circulationService;

    public function __construct()
    {
        $this->loanModel = new LoanTransactionModel();
        $this->circulationService = new CirculationService();
    }

    public function index(Request $request): Response
    {
        $sessionUser = Session::getUser();
        $userId = (int)($sessionUser['id'] ?? 0);

        $activeLoans = [];
        $recentHistory = [];
        $overdueCount = 0;

        try {
            $activeLoans = $this->loanModel->getActiveLoansByUserId($userId);
            $recentHistory = $this->loanModel->getRecentHistoryByUserId($userId, 15);
            $overdueCount = $this->loanModel->countOverdueLoansByUserId($userId);
        } catch (Throwable $e) {
            $activeLoans = [];
            $recentHistory = [];
        }

        return $this->render('pages/member/loans/index', [
            'pageTitle' => 'Peminjaman Buku Fisik',
            'activeLoans' => $activeLoans,
            'recentHistory' => $recentHistory,
            'overdueCount' => $overdueCount,
        ]);
    }

    public function renew(Request $request, string $loanId): Response
    {
        $sessionUser = Session::getUser();
        $userId = (int)($sessionUser['id'] ?? 0);

        try {
            $result = $this->circulationService->renew((int)$loanId, $userId, false);
            Session::setFlash('success', "Peminjaman berhasil diperpanjang. Tanggal jatuh tempo baru Anda adalah: {$result['new_due_date']}.");
        } catch (Throwable $e) {
            Session::setFlash('error', $e->getMessage());
        }

        return $this->redirect('/member/loans');
    }
}
