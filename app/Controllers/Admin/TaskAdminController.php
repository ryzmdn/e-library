<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\AuditLogModel;
use App\Models\LoanTransactionModel;
use App\Models\ReservationQueueModel;
use App\Services\ScheduledTaskService;
use Throwable;

final class TaskAdminController extends Controller
{
    private ScheduledTaskService $taskService;
    private LoanTransactionModel $loanModel;
    private ReservationQueueModel $reservationModel;
    private AuditLogModel $auditModel;

    public function __construct()
    {
        $this->taskService = new ScheduledTaskService();
        $this->loanModel = new LoanTransactionModel();
        $this->reservationModel = new ReservationQueueModel();
        $this->auditModel = new AuditLogModel();
    }

    public function index(Request $request): Response
    {
        $overdueCount = 0;
        $expiredReservationCount = 0;
        $taskLogs = [];

        try {
            $overdueCount = $this->loanModel->countOverdueLoans();
            $expiredReservations = $this->reservationModel->getExpiredPickups();
            $expiredReservationCount = count($expiredReservations);
            $taskLogs = $this->auditModel->getTaskLogs(15);
        } catch (Throwable $e) {
        }

        return $this->render('pages/admin/tasks/index', [
            'pageTitle' => 'Manajemen Tugas Terjadwal (Cron Jobs)',
            'overdueCount' => $overdueCount,
            'expiredReservationCount' => $expiredReservationCount,
            'taskLogs' => $taskLogs,
        ], 'admin');
    }

    public function runFines(Request $request): Response
    {
        $user = Session::get('user');
        $staffId = $user ? (int)$user['id'] : null;

        try {
            $result = $this->taskService->calculateDailyFines(null, $staffId);
            $msg = 'Kalkulasi denda selesai: ' . $result['fines_assessed_count'] . ' denda baru dinilai (+Rp ' . number_format($result['total_fine_amount'], 0, ',', '.') . ').';
            Session::flash('success', $msg);
        } catch (Throwable $e) {
            Session::flash('error', 'Gagal menjalankan kalkulasi denda: ' . $e->getMessage());
        }

        return $this->redirect('/admin/tasks');
    }

    public function runReservations(Request $request): Response
    {
        $user = Session::get('user');
        $staffId = $user ? (int)$user['id'] : null;

        try {
            $result = $this->taskService->expirePendingReservations($staffId);
            $msg = 'Pembersihan reservasi selesai: ' . $result['total_expired'] . ' kedaluwarsa diproses (' . $result['reassigned_count'] . ' dialihkan, ' . $result['released_to_shelf_count'] . ' kembali ke rak).';
            Session::flash('success', $msg);
        } catch (Throwable $e) {
            Session::flash('error', 'Gagal memproses kedaluwarsa reservasi: ' . $e->getMessage());
        }

        return $this->redirect('/admin/tasks');
    }

    public function runAll(Request $request): Response
    {
        $user = Session::get('user');
        $staffId = $user ? (int)$user['id'] : null;

        try {
            $result = $this->taskService->runAll($staffId);
            $fines = $result['fines'];
            $res = $result['reservations'];
            $msg = 'Semua tugas berhasil dieksekusi. Denda baru: ' . $fines['fines_assessed_count'] . ' (+Rp ' . number_format($fines['total_fine_amount'], 0, ',', '.') . '), Reservasi kedaluwarsa: ' . $res['total_expired'] . '.';
            Session::flash('success', $msg);
        } catch (Throwable $e) {
            Session::flash('error', 'Gagal menjalankan tugas terjadwal: ' . $e->getMessage());
        }

        return $this->redirect('/admin/tasks');
    }
}
