<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLogModel;
use App\Models\BookCopyModel;
use App\Models\FineLedgerModel;
use App\Models\LoanTransactionModel;
use App\Models\ReservationQueueModel;
use Throwable;

final class ScheduledTaskService
{
    private LoanTransactionModel $loanModel;
    private FineLedgerModel $fineModel;
    private ReservationQueueModel $reservationModel;
    private BookCopyModel $copyModel;
    private HolidayService $holidayService;
    private FineCalculatorService $fineCalculator;
    private AuditLogModel $auditModel;

    public function __construct(
        ?LoanTransactionModel $loanModel = null,
        ?FineLedgerModel $fineModel = null,
        ?ReservationQueueModel $reservationModel = null,
        ?BookCopyModel $copyModel = null,
        ?HolidayService $holidayService = null,
        ?FineCalculatorService $fineCalculator = null,
        ?AuditLogModel $auditModel = null
    ) {
        $this->loanModel = $loanModel ?? new LoanTransactionModel();
        $this->fineModel = $fineModel ?? new FineLedgerModel();
        $this->reservationModel = $reservationModel ?? new ReservationQueueModel();
        $this->copyModel = $copyModel ?? new BookCopyModel();
        $this->holidayService = $holidayService ?? new HolidayService();
        $this->fineCalculator = $fineCalculator ?? new FineCalculatorService();
        $this->auditModel = $auditModel ?? new AuditLogModel();
    }

    public function calculateDailyFines(?string $today = null, ?int $actorUserId = null): array
    {
        $today = $today ?? date('Y-m-d');
        $overdueLoans = $this->loanModel->getOverdueLoans();

        $stats = [
            'total_scanned' => count($overdueLoans),
            'fines_assessed_count' => 0,
            'total_fine_amount' => 0.00,
            'details' => [],
        ];

        foreach ($overdueLoans as $loan) {
            $loanId = (int)$loan['id'];
            $userId = (int)$loan['user_id'];
            $dueDate = (string)$loan['due_date'];
            $dailyRate = (float)($loan['daily_fine_amount'] ?? 1000.00);
            $cap = (float)($loan['max_fine_cap'] ?? 50000.00);

            $overdueDays = $this->holidayService->calculateOverdueDays($dueDate, $today);
            if ($overdueDays <= 0) {
                continue;
            }

            $totalCalculatedFine = $this->fineCalculator->calculate($overdueDays, $dailyRate, $cap);
            $alreadyAssessed = $this->fineModel->getTotalAssessedFineForLoan($loanId);
            $diff = max(0.00, round($totalCalculatedFine - $alreadyAssessed, 2));

            $this->loanModel->updateOverdueStatus($loanId, $totalCalculatedFine);

            if ($diff > 0.00) {
                $notes = 'Kalkulasi denda harian otomatis keterlambatan ' . $overdueDays . ' hari kerja per ' . $today;
                $this->fineModel->recordAssessment($loanId, $userId, $diff, $notes);

                $this->auditModel->log(
                    $actorUserId,
                    'SYSTEM_DAILY_FINE_ASSESSMENT',
                    'fine_ledgers',
                    $loanId,
                    [
                        'overdue_days' => $overdueDays,
                        'added_fine' => $diff,
                        'total_fine' => $totalCalculatedFine,
                    ]
                );

                $stats['fines_assessed_count']++;
                $stats['total_fine_amount'] += $diff;

                $stats['details'][] = [
                    'loan_id' => $loanId,
                    'transaction_code' => $loan['transaction_code'] ?? '',
                    'member_name' => $loan['member_name'] ?? '',
                    'book_title' => $loan['title'] ?? '',
                    'overdue_days' => $overdueDays,
                    'added_fine' => $diff,
                    'total_fine' => $totalCalculatedFine,
                ];
            }
        }

        $stats['total_fine_amount'] = round($stats['total_fine_amount'], 2);
        return $stats;
    }

    public function expirePendingReservations(?int $actorUserId = null): array
    {
        $expiredPickups = $this->reservationModel->getExpiredPickups();

        $stats = [
            'total_expired' => count($expiredPickups),
            'reassigned_count' => 0,
            'released_to_shelf_count' => 0,
            'details' => [],
        ];

        foreach ($expiredPickups as $res) {
            $resId = (int)$res['id'];
            $biblioId = (int)$res['biblio_id'];
            $copyId = !empty($res['assigned_copy_id']) ? (int)$res['assigned_copy_id'] : null;

            $this->reservationModel->markAsExpired($resId);

            $actionTaken = 'EXPIRED_NO_COPY';

            if ($copyId !== null) {
                $nextWaiting = $this->reservationModel->getNextWaitingReservation($biblioId);

                if ($nextWaiting !== null) {
                    $nextResId = (int)$nextWaiting['id'];
                    $this->reservationModel->assignCopyToReservation($nextResId, $copyId, 2);

                    $this->auditModel->log(
                        $actorUserId,
                        'SYSTEM_REASSIGN_RESERVATION',
                        'reservation_queues',
                        $nextResId,
                        [
                            'previous_expired_reservation_id' => $resId,
                            'copy_id' => $copyId,
                            'new_user_id' => $nextWaiting['user_id'],
                        ]
                    );

                    $actionTaken = 'REASSIGNED_TO_NEXT_MEMBER';
                    $stats['reassigned_count']++;
                } else {
                    $this->copyModel->updateCopy($copyId, [
                        'availability_status' => 'AVAILABLE',
                    ]);
                    $this->copyModel->syncBiblioAvailableCopies($biblioId);

                    $this->auditModel->log(
                        $actorUserId,
                        'SYSTEM_RELEASE_RESERVATION_COPY',
                        'book_copies',
                        $copyId,
                        [
                            'expired_reservation_id' => $resId,
                            'status_restored' => 'AVAILABLE',
                        ]
                    );

                    $actionTaken = 'RETURNED_TO_SHELF';
                    $stats['released_to_shelf_count']++;
                }
            }

            $stats['details'][] = [
                'reservation_id' => $resId,
                'biblio_id' => $biblioId,
                'copy_id' => $copyId,
                'barcode' => $res['inventory_barcode'] ?? '',
                'action_taken' => $actionTaken,
            ];
        }

        return $stats;
    }

    public function runAll(?int $actorUserId = null): array
    {
        return [
            'fines' => $this->calculateDailyFines(null, $actorUserId),
            'reservations' => $this->expirePendingReservations($actorUserId),
        ];
    }
}
