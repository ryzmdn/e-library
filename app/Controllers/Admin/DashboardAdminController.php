<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\AuditLogModel;
use App\Models\BibliographicItemModel;
use App\Models\BookCopyModel;
use App\Models\FineLedgerModel;
use App\Models\LoanTransactionModel;
use App\Models\UserModel;
use Throwable;

final class DashboardAdminController extends Controller
{
    private BibliographicItemModel $biblioModel;
    private BookCopyModel $copyModel;
    private UserModel $userModel;
    private LoanTransactionModel $loanModel;
    private FineLedgerModel $fineModel;
    private AuditLogModel $auditModel;

    public function __construct()
    {
        $this->biblioModel = new BibliographicItemModel();
        $this->copyModel = new BookCopyModel();
        $this->userModel = new UserModel();
        $this->loanModel = new LoanTransactionModel();
        $this->fineModel = new FineLedgerModel();
        $this->auditModel = new AuditLogModel();
    }

    public function index(Request $request): Response
    {
        $stats = [
            'totalTitles' => 0,
            'totalCopies' => 0,
            'totalMembers' => 0,
            'activeLoans' => 0,
            'overdueLoans' => 0,
            'todayCheckouts' => 0,
            'todayCheckins' => 0,
            'unpaidUsers' => 0,
        ];

        $recentLoans = [];
        $recentLogs = [];

        try {
            $stats['totalTitles'] = $this->biblioModel->countAllItemsAdmin();
            $stats['totalCopies'] = $this->copyModel->countAllCopiesAdmin();
            $stats['totalMembers'] = $this->userModel->countActiveMembers();
            $stats['activeLoans'] = $this->loanModel->countActiveLoans();
            $stats['overdueLoans'] = $this->loanModel->countOverdueLoans();
            $stats['todayCheckouts'] = $this->loanModel->countTodayCheckouts();
            $stats['todayCheckins'] = $this->loanModel->countTodayCheckins();
            $stats['unpaidUsers'] = $this->fineModel->countUnpaidUsers();

            $recentLoans = $this->loanModel->getAllLoans([], 6, 0);
            $recentLogs = $this->auditModel->getRecentLogs(6, 0);
        } catch (Throwable $e) {
        }

        return $this->render('pages/admin/dashboard', [
            'pageTitle' => 'Dasbor Administrasi',
            'stats' => $stats,
            'recentLoans' => $recentLoans,
            'recentLogs' => $recentLogs,
        ], 'admin');
    }
}
