<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\FineLedgerModel;
use App\Services\CirculationService;
use Throwable;

final class FineAdminController extends Controller
{
    private FineLedgerModel $fineModel;
    private CirculationService $circulationService;

    public function __construct()
    {
        $this->fineModel = new FineLedgerModel();
        $this->circulationService = new CirculationService();
    }

    public function index(Request $request): Response
    {
        $unpaidUsers = [];
        $recentPayments = [];
        $totalUnpaidCount = 0;

        try {
            $unpaidUsers = $this->fineModel->getUnpaidUsersList(20, 0);
            $totalUnpaidCount = $this->fineModel->countUnpaidUsers();
            $recentPayments = $this->fineModel->getRecentPayments(15);
        } catch (Throwable $e) {
            $unpaidUsers = [];
            $recentPayments = [];
        }

        return $this->render('pages/admin/fines/index', [
            'pageTitle' => 'Manajemen & Pembayaran Denda',
            'unpaidUsers' => $unpaidUsers,
            'totalUnpaidCount' => $totalUnpaidCount,
            'recentPayments' => $recentPayments,
        ], 'admin');
    }

    public function pay(Request $request): Response
    {
        $staffUser = Session::getUser();
        $staffId = (int)($staffUser['id'] ?? 0);

        $loanId = (int)$request->input('loan_id', 0);
        $userId = (int)$request->input('user_id', 0);
        $amount = (float)$request->input('amount', 0.0);
        $paymentType = trim((string)$request->input('payment_type', 'PAYMENT_CASH'));
        $receipt = trim((string)$request->input('receipt_reference', ''));
        $notes = trim((string)$request->input('notes', ''));

        if ($loanId <= 0 || $userId <= 0 || $amount <= 0.0) {
            Session::setFlash('error', 'Informasi transaksi dan nominal pembayaran tidak valid.');
            return $this->redirect('/admin/fines');
        }

        try {
            $result = $this->circulationService->payFine(
                $staffId,
                $loanId,
                $userId,
                $amount,
                $paymentType,
                $receipt !== '' ? $receipt : null,
                $notes !== '' ? $notes : null
            );

            $paidFormatted = number_format($result['amount_paid'], 2, ',', '.');
            $remFormatted = number_format($result['remaining_balance'], 2, ',', '.');
            Session::setFlash('success', "Pembayaran sebesar Rp {$paidFormatted} berhasil dicatat. Sisa saldo denda pinjaman ini: Rp {$remFormatted}.");
        } catch (Throwable $e) {
            Session::setFlash('error', $e->getMessage());
        }

        return $this->redirect('/admin/fines');
    }
}
