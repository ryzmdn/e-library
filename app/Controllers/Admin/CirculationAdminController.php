<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\LoanTransactionModel;
use App\Services\CirculationService;
use Throwable;

final class CirculationAdminController extends Controller
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
        $todayCheckouts = 0;
        $todayCheckins = 0;
        $overdueLoans = [];
        $recentLoans = [];

        try {
            $todayCheckouts = $this->loanModel->countTodayCheckouts();
            $todayCheckins = $this->loanModel->countTodayCheckins();
            $overdueLoans = $this->loanModel->getOverdueLoans();
            $recentLoans = $this->loanModel->getAllLoans([], 10, 0);
        } catch (Throwable $e) {
            $recentLoans = [];
        }

        return $this->render('pages/admin/circulation/index', [
            'pageTitle' => 'Meja Layanan Sirkulasi Buku',
            'todayCheckouts' => $todayCheckouts,
            'todayCheckins' => $todayCheckins,
            'overdueLoans' => $overdueLoans,
            'recentLoans' => $recentLoans,
        ], 'admin');
    }

    public function checkout(Request $request): Response
    {
        $staffUser = Session::getUser();
        $staffId = (int)($staffUser['id'] ?? 0);

        $memberIdentifier = trim((string)$request->input('member_identifier', ''));
        $copyBarcode = trim((string)$request->input('copy_barcode', ''));

        if ($memberIdentifier === '' || $copyBarcode === '') {
            Session::setFlash('error', 'Nomor anggota dan barcode buku wajib diisi.');
            return $this->redirect('/admin/circulation');
        }

        try {
            $result = $this->circulationService->checkout($staffId, $memberIdentifier, $copyBarcode);
            Session::setFlash('success', "Peminjaman berhasil dicatat! Kode: {$result['transaction_code']} &mdash; Buku: \"{$result['book_title']}\" untuk {$result['member_name']}. Jatuh tempo: {$result['due_date']}.");
        } catch (Throwable $e) {
            Session::setFlash('error', $e->getMessage());
        }

        return $this->redirect('/admin/circulation');
    }

    public function checkin(Request $request): Response
    {
        $staffUser = Session::getUser();
        $staffId = (int)($staffUser['id'] ?? 0);

        $copyBarcode = trim((string)$request->input('copy_barcode', ''));
        $condition = trim((string)$request->input('condition', 'GOOD'));
        $notes = trim((string)$request->input('notes', ''));

        if ($copyBarcode === '') {
            Session::setFlash('error', 'Barcode buku wajib dimasukkan.');
            return $this->redirect('/admin/circulation');
        }

        try {
            $result = $this->circulationService->checkin($staffId, $copyBarcode, $condition, $notes !== '' ? $notes : null);
            $msg = "Pengembalian berhasil diproses untuk barcode {$result['barcode']}.";
            if (!empty($result['accrued_fine']) && $result['accrued_fine'] > 0) {
                $fineFormatted = number_format((float)$result['accrued_fine'], 2, ',', '.');
                $msg .= " Terkena denda keterlambatan sebesar Rp {$fineFormatted}.";
            }
            if (!empty($result['assigned_reservation'])) {
                $msg .= " Eksemplar ini dialihkan ke antrean hold untuk {$result['assigned_reservation']['member_name']} ({$result['assigned_reservation']['member_number']}).";
            }
            Session::setFlash('success', $msg);
        } catch (Throwable $e) {
            Session::setFlash('error', $e->getMessage());
        }

        return $this->redirect('/admin/circulation');
    }

    public function history(Request $request): Response
    {
        $status = trim((string)$request->input('status', ''));
        $keyword = trim((string)$request->input('q', ''));
        $page = max(1, (int)$request->input('page', 1));
        $limit = 15;

        $filters = [];
        if ($status !== '') {
            $filters['status'] = $status;
        }
        if ($keyword !== '') {
            $filters['keyword'] = $keyword;
        }

        $loans = [];
        $totalLoans = 0;
        $totalPages = 1;

        try {
            $totalLoans = $this->loanModel->countAllLoans($filters);
            $totalPages = max(1, (int)ceil($totalLoans / $limit));
            if ($page > $totalPages) {
                $page = $totalPages;
            }
            $offset = ($page - 1) * $limit;
            $loans = $this->loanModel->getAllLoans($filters, $limit, $offset);
        } catch (Throwable $e) {
            $loans = [];
        }

        return $this->render('pages/admin/circulation/history', [
            'pageTitle' => 'Log Transaksi Sirkulasi Lengkap',
            'loans' => $loans,
            'totalLoans' => $totalLoans,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'status' => $status,
            'keyword' => $keyword,
        ], 'admin');
    }
}
