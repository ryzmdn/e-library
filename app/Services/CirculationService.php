<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DatabaseTransaction;
use App\Models\AuditLogModel;
use App\Models\BibliographicItemModel;
use App\Models\BookCopyModel;
use App\Models\FineLedgerModel;
use App\Models\HolidayModel;
use App\Models\LoanTransactionModel;
use App\Models\MembershipTierModel;
use App\Models\ReservationQueueModel;
use App\Models\UserModel;
use RuntimeException;
use Throwable;

final class CirculationService
{
    private UserModel $userModel;
    private MembershipTierModel $tierModel;
    private BookCopyModel $copyModel;
    private BibliographicItemModel $biblioModel;
    private LoanTransactionModel $loanModel;
    private FineLedgerModel $fineModel;
    private ReservationQueueModel $reservationModel;
    private HolidayService $holidayService;
    private FineCalculatorService $fineCalculator;
    private AuditLogModel $auditModel;

    public function __construct(
        ?UserModel $userModel = null,
        ?MembershipTierModel $tierModel = null,
        ?BookCopyModel $copyModel = null,
        ?BibliographicItemModel $biblioModel = null,
        ?LoanTransactionModel $loanModel = null,
        ?FineLedgerModel $fineModel = null,
        ?ReservationQueueModel $reservationModel = null,
        ?HolidayService $holidayService = null,
        ?FineCalculatorService $fineCalculator = null,
        ?AuditLogModel $auditModel = null
    ) {
        $this->userModel = $userModel ?? new UserModel();
        $this->tierModel = $tierModel ?? new MembershipTierModel();
        $this->copyModel = $copyModel ?? new BookCopyModel();
        $this->biblioModel = $biblioModel ?? new BibliographicItemModel();
        $this->loanModel = $loanModel ?? new LoanTransactionModel();
        $this->fineModel = $fineModel ?? new FineLedgerModel();
        $this->reservationModel = $reservationModel ?? new ReservationQueueModel();
        $this->holidayService = $holidayService ?? new HolidayService();
        $this->fineCalculator = $fineCalculator ?? new FineCalculatorService();
        $this->auditModel = $auditModel ?? new AuditLogModel();
    }

    public function checkout(int $staffId, string $memberIdentifier, string $copyBarcode): array
    {
        $user = $this->userModel->findByIdentifier($memberIdentifier);
        if (!$user) {
            throw new RuntimeException('Anggota dengan identitas tersebut tidak ditemukan.');
        }

        $userId = (int)$user['id'];
        $accountStatus = (string)($user['account_status'] ?? 'PENDING_VERIFICATION');
        if ($accountStatus !== 'ACTIVE') {
            throw new RuntimeException("Akun anggota berstatus {$accountStatus} dan tidak diizinkan melakukan peminjaman.");
        }

        $expiresAt = (string)($user['membership_expires_at'] ?? '1970-01-01');
        if ($expiresAt < date('Y-m-d')) {
            throw new RuntimeException('Masa berlaku keanggotaan perpustakaan telah berakhir. Silakan perpanjang akun terlebih dahulu.');
        }

        $unpaidFine = $this->fineModel->getTotalUnpaidFine($userId);
        if ($unpaidFine > 0.0) {
            $formattedFine = number_format($unpaidFine, 2, ',', '.');
            throw new RuntimeException("Peminjaman ditangguhkan: Anggota memiliki tunggakan denda sebesar Rp {$formattedFine}.");
        }

        $overdueLoans = $this->loanModel->countOverdueLoansByUserId($userId);
        if ($overdueLoans > 0) {
            throw new RuntimeException('Peminjaman ditangguhkan: Anggota masih memiliki pinjaman aktif yang melewati tanggal jatuh tempo.');
        }

        $tier = $this->tierModel->findById((int)$user['tier_id']);
        if (!$tier) {
            $tier = $this->tierModel->getDefaultTier();
        }

        $maxLoans = (int)($tier['max_physical_loans'] ?? 3);
        $activeLoansCount = $this->loanModel->countActiveLoansByUserId($userId);
        if ($activeLoansCount >= $maxLoans) {
            throw new RuntimeException("Batas kuota peminjaman tercapai ({$activeLoansCount} dari {$maxLoans} buku telah dipinjam).");
        }

        $copy = $this->copyModel->findByBarcode($copyBarcode);
        if (!$copy) {
            throw new RuntimeException('Eksemplar buku dengan barcode tersebut tidak ditemukan.');
        }

        $copyId = (int)$copy['id'];
        $biblioId = (int)$copy['biblio_id'];
        $copyStatus = (string)$copy['availability_status'];

        $reservedQueueId = null;
        if ($copyStatus === 'RESERVED_HOLD') {
            $assignedReservation = $this->findActiveReservationForUserCopy($biblioId, $userId, $copyId);
            if (!$assignedReservation) {
                throw new RuntimeException('Eksemplar buku ini sedang disiapkan untuk anggota lain dalam antrean reservasi.');
            }
            $reservedQueueId = (int)$assignedReservation['id'];
        } elseif ($copyStatus !== 'AVAILABLE') {
            throw new RuntimeException("Eksemplar buku tidak dapat dipinjam karena berstatus: {$copyStatus}.");
        }

        $durationDays = (int)($tier['loan_duration_days'] ?? 7);
        $startDate = date('Y-m-d');
        $dueDate = $this->holidayService->calculateDueDate($startDate, $durationDays);

        return DatabaseTransaction::run(function () use ($staffId, $userId, $copyId, $biblioId, $dueDate, $startDate, $reservedQueueId, $copy, $user): array {
            $trxCode = $this->loanModel->generateTransactionCode();
            $idempotencyKey = hash('sha256', "{$userId}-{$copyId}-" . time());

            $loanId = $this->loanModel->createLoan([
                'transaction_code' => $trxCode,
                'idempotency_key' => $idempotencyKey,
                'user_id' => $userId,
                'book_copy_id' => $copyId,
                'issued_by_staff_id' => $staffId,
                'borrowed_at' => date('Y-m-d H:i:s'),
                'due_date' => $dueDate,
            ]);

            $this->copyModel->updateAvailabilityStatus($copyId, 'BORROWED');

            if ($reservedQueueId !== null) {
                $this->reservationModel->markAsFulfilled($reservedQueueId);
            }

            $this->copyModel->syncBiblioAvailableCopies($biblioId);

            $this->auditModel->log(
                $staffId,
                'LOAN_CHECKOUT',
                'loan_transactions',
                $loanId,
                [
                    'member_id' => $userId,
                    'copy_barcode' => $copy['inventory_barcode'],
                    'due_date' => $dueDate,
                    'transaction_code' => $trxCode,
                ]
            );

            return [
                'loan_id' => $loanId,
                'transaction_code' => $trxCode,
                'due_date' => $dueDate,
                'member_name' => $user['full_name'],
                'book_title' => $copy['title'],
                'barcode' => $copy['inventory_barcode'],
            ];
        });
    }

    public function checkin(int $staffId, string $copyBarcode, string $condition = 'GOOD', ?string $notes = null): array
    {
        $copy = $this->copyModel->findByBarcode($copyBarcode);
        if (!$copy) {
            throw new RuntimeException('Eksemplar buku dengan barcode tersebut tidak ditemukan.');
        }

        $copyId = (int)$copy['id'];
        $biblioId = (int)$copy['biblio_id'];

        $activeLoan = $this->loanModel->getActiveLoanByCopyId($copyId);
        if (!$activeLoan) {
            if ($copy['availability_status'] === 'BORROWED') {
                $this->copyModel->updateAvailabilityStatus($copyId, 'AVAILABLE');
                $this->copyModel->syncBiblioAvailableCopies($biblioId);
                return [
                    'message' => 'Status eksemplar berhasil diperbaiki menjadi tersedia di rak.',
                    'barcode' => $copy['inventory_barcode'],
                    'accrued_fine' => 0.00,
                ];
            }
            throw new RuntimeException('Eksemplar buku ini tidak sedang dalam status peminjaman aktif.');
        }

        $loanId = (int)$activeLoan['id'];
        $userId = (int)$activeLoan['user_id'];
        $returnDate = date('Y-m-d');
        $dueDate = (string)$activeLoan['due_date'];

        $user = $this->userModel->findById($userId);
        $tier = $this->tierModel->findById((int)($user['tier_id'] ?? 1)) ?? $this->tierModel->getDefaultTier();

        $accruedFine = 0.00;
        if ($returnDate > $dueDate) {
            $accruedFine = $this->fineCalculator->assessFineForLoan($activeLoan, $tier, $returnDate, $this->holidayService);
        }

        return DatabaseTransaction::run(function () use (
            $staffId, $loanId, $userId, $copyId, $biblioId, $condition, $notes,
            $accruedFine, $activeLoan, $copy
        ): array {
            if ($accruedFine > 0.0) {
                $this->fineModel->recordAssessment(
                    $loanId,
                    $userId,
                    $accruedFine,
                    "Keterlambatan pengembalian buku ({$copy['inventory_barcode']})"
                );
            }

            $fineSettled = ($accruedFine <= 0.0);
            $this->loanModel->markAsReturned($loanId, $staffId, $condition, $accruedFine, $fineSettled, $notes);

            $nextWaiting = $this->reservationModel->getNextWaitingReservation($biblioId);
            $nextAssigned = null;

            if ($nextWaiting !== null) {
                $this->reservationModel->assignCopyToReservation((int)$nextWaiting['id'], $copyId, 2);
                $this->copyModel->updateAvailabilityStatus($copyId, 'RESERVED_HOLD');
                $nextAssigned = [
                    'member_name' => $nextWaiting['member_name'],
                    'member_number' => $nextWaiting['member_number'],
                ];
            } else {
                $this->copyModel->updateAvailabilityStatus($copyId, 'AVAILABLE');
            }

            $this->copyModel->syncBiblioAvailableCopies($biblioId);

            $this->auditModel->log(
                $staffId,
                'LOAN_CHECKIN',
                'loan_transactions',
                $loanId,
                [
                    'copy_barcode' => $copy['inventory_barcode'],
                    'accrued_fine' => $accruedFine,
                    'condition' => $condition,
                    'assigned_to_queue' => $nextAssigned !== null,
                ]
            );

            return [
                'loan_id' => $loanId,
                'transaction_code' => $activeLoan['transaction_code'],
                'book_title' => $activeLoan['title'],
                'barcode' => $copy['inventory_barcode'],
                'member_name' => $activeLoan['member_name'],
                'accrued_fine' => $accruedFine,
                'assigned_reservation' => $nextAssigned,
            ];
        });
    }

    public function renew(int $loanId, int $actorUserId, bool $isStaff = false): array
    {
        $loan = $this->loanModel->findById($loanId);
        if (!$loan) {
            throw new RuntimeException('Transaksi peminjaman tidak ditemukan.');
        }

        if (!$isStaff && (int)$loan['user_id'] !== $actorUserId) {
            throw new RuntimeException('Akses ditolak: Anda tidak memiliki wewenang untuk memperpanjang pinjaman ini.');
        }

        $status = (string)$loan['loan_status'];
        if ($status !== 'ACTIVE') {
            throw new RuntimeException("Pinjaman tidak dapat diperpanjang karena berstatus {$status}.");
        }

        $today = date('Y-m-d');
        if ((string)$loan['due_date'] < $today) {
            throw new RuntimeException('Buku yang telah melewati jatuh tempo tidak dapat diperpanjang. Silakan kembalikan di meja sirkulasi.');
        }

        $maxRenewals = (int)($loan['max_renewals'] ?? 1);
        $currentRenewals = (int)($loan['renewal_count'] ?? 0);
        if ($currentRenewals >= $maxRenewals) {
            throw new RuntimeException("Batas maksimal perpanjangan ({$maxRenewals} kali) untuk buku ini telah tercapai.");
        }

        $biblioId = (int)$loan['biblio_id'];
        $hasReservation = $this->reservationModel->getNextWaitingReservation($biblioId);
        if ($hasReservation !== null) {
            throw new RuntimeException('Buku tidak dapat diperpanjang karena terdapat anggota lain dalam antrean reservasi.');
        }

        $durationDays = (int)($loan['loan_duration_days'] ?? 7);
        $newDueDate = $this->holidayService->calculateDueDate((string)$loan['due_date'], $durationDays);

        return DatabaseTransaction::run(function () use ($loanId, $actorUserId, $newDueDate, $loan): array {
            $this->loanModel->recordRenewal($loanId, $newDueDate);

            $this->auditModel->log(
                $actorUserId,
                'LOAN_RENEWAL',
                'loan_transactions',
                $loanId,
                [
                    'old_due_date' => $loan['due_date'],
                    'new_due_date' => $newDueDate,
                    'renewal_count' => (int)$loan['renewal_count'] + 1,
                ]
            );

            return [
                'loan_id' => $loanId,
                'new_due_date' => $newDueDate,
                'renewal_count' => (int)$loan['renewal_count'] + 1,
            ];
        });
    }

    public function reserve(int $biblioId, int $userId): array
    {
        $user = $this->userModel->findById($userId);
        if (!$user) {
            throw new RuntimeException('Data anggota tidak ditemukan.');
        }

        $accountStatus = (string)($user['account_status'] ?? 'PENDING_VERIFICATION');
        if ($accountStatus !== 'ACTIVE') {
            throw new RuntimeException("Akun berstatus {$accountStatus} tidak dapat membuat reservasi buku.");
        }

        $unpaidFine = $this->fineModel->getTotalUnpaidFine($userId);
        if ($unpaidFine > 0.0) {
            throw new RuntimeException('Reservasi ditangguhkan: Selesaikan tunggakan denda sebelum membuat antrean baru.');
        }

        $tier = $this->tierModel->findById((int)$user['tier_id']) ?? $this->tierModel->getDefaultTier();
        $maxReservations = (int)($tier['max_active_reservations'] ?? 2);
        $activeResCount = $this->reservationModel->countActiveReservationsByUserId($userId);
        if ($activeResCount >= $maxReservations) {
            throw new RuntimeException("Batas maksimal antrean reservasi aktif ({$maxReservations} judul) telah tercapai.");
        }

        if ($this->reservationModel->isUserAlreadyQueued($biblioId, $userId)) {
            throw new RuntimeException('Anda sudah memiliki antrean aktif untuk judul buku ini.');
        }

        $biblio = $this->biblioModel->findById($biblioId);
        if (!$biblio) {
            throw new RuntimeException('Koleksi bibliografi tidak ditemukan.');
        }

        if ((int)$biblio['total_physical_copies'] === 0) {
            throw new RuntimeException('Koleksi ini tidak memiliki eksemplar fisik yang dapat diantre.');
        }

        if ((int)$biblio['available_physical_copies'] > 0) {
            throw new RuntimeException('Buku masih tersedia di rak fisik. Silakan kunjungi perpustakaan untuk meminjam langsung.');
        }

        return DatabaseTransaction::run(function () use ($biblioId, $userId, $biblio): array {
            $queueId = $this->reservationModel->createReservation($biblioId, $userId);
            $position = $this->reservationModel->getQueuePosition($biblioId, $userId);

            $this->auditModel->log(
                $userId,
                'RESERVATION_CREATED',
                'reservation_queues',
                $queueId,
                [
                    'biblio_id' => $biblioId,
                    'book_title' => $biblio['title'],
                    'queue_position' => $position,
                ]
            );

            return [
                'queue_id' => $queueId,
                'book_title' => $biblio['title'],
                'queue_position' => $position,
            ];
        });
    }

    public function cancelReservation(int $reservationId, int $userId): bool
    {
        $reservation = $this->reservationModel->findById($reservationId);
        if (!$reservation) {
            throw new RuntimeException('Antrean reservasi tidak ditemukan.');
        }

        if ((int)$reservation['user_id'] !== $userId) {
            throw new RuntimeException('Akses ditolak: Anda tidak memiliki wewenang untuk membatalkan antrean ini.');
        }

        $assignedCopyId = $reservation['assigned_copy_id'] !== null ? (int)$reservation['assigned_copy_id'] : null;
        $biblioId = (int)$reservation['biblio_id'];

        return DatabaseTransaction::run(function () use ($reservationId, $userId, $assignedCopyId, $biblioId): bool {
            $cancelled = $this->reservationModel->markAsCancelled($reservationId, $userId);

            if ($cancelled && $assignedCopyId !== null) {
                $next = $this->reservationModel->getNextWaitingReservation($biblioId);
                if ($next !== null) {
                    $this->reservationModel->assignCopyToReservation((int)$next['id'], $assignedCopyId, 2);
                } else {
                    $this->copyModel->updateAvailabilityStatus($assignedCopyId, 'AVAILABLE');
                    $this->copyModel->syncBiblioAvailableCopies($biblioId);
                }
            }

            $this->auditModel->log(
                $userId,
                'RESERVATION_CANCELLED',
                'reservation_queues',
                $reservationId
            );

            return $cancelled;
        });
    }

    public function payFine(int $staffId, int $loanId, int $userId, float $amount, string $paymentType, ?string $receipt = null, ?string $notes = null): array
    {
        if ($amount <= 0.0) {
            throw new RuntimeException('Jumlah pembayaran denda harus lebih besar dari nol.');
        }

        $outstanding = $this->fineModel->getOutstandingBalanceForLoan($loanId);
        if ($outstanding <= 0.0) {
            throw new RuntimeException('Pinjaman ini tidak memiliki saldo denda yang tertunggak.');
        }

        $payAmount = min($amount, $outstanding);

        return DatabaseTransaction::run(function () use ($staffId, $loanId, $userId, $payAmount, $paymentType, $receipt, $notes, $outstanding): array {
            $ledgerId = $this->fineModel->recordPayment(
                $loanId,
                $userId,
                $paymentType,
                $payAmount,
                $staffId,
                $receipt,
                $notes
            );

            $newBalance = max(0.00, round($outstanding - $payAmount, 2));
            if ($newBalance <= 0.0) {
                $this->loanModel->markFineAsSettled($loanId);
            }

            $this->auditModel->log(
                $staffId,
                'FINE_PAYMENT',
                'fine_ledgers',
                $ledgerId,
                [
                    'loan_id' => $loanId,
                    'member_id' => $userId,
                    'amount_paid' => $payAmount,
                    'remaining_balance' => $newBalance,
                    'payment_type' => $paymentType,
                ]
            );

            return [
                'ledger_id' => $ledgerId,
                'amount_paid' => $payAmount,
                'remaining_balance' => $newBalance,
            ];
        });
    }

    public function processExpiredReservations(): int
    {
        $expired = $this->reservationModel->getExpiredPickups();
        $count = 0;

        foreach ($expired as $item) {
            $resId = (int)$item['id'];
            $biblioId = (int)$item['biblio_id'];
            $copyId = $item['assigned_copy_id'] !== null ? (int)$item['assigned_copy_id'] : null;

            DatabaseTransaction::run(function () use ($resId, $biblioId, $copyId): void {
                $this->reservationModel->markAsExpired($resId);

                if ($copyId !== null) {
                    $next = $this->reservationModel->getNextWaitingReservation($biblioId);
                    if ($next !== null) {
                        $this->reservationModel->assignCopyToReservation((int)$next['id'], $copyId, 2);
                    } else {
                        $this->copyModel->updateAvailabilityStatus($copyId, 'AVAILABLE');
                        $this->copyModel->syncBiblioAvailableCopies($biblioId);
                    }
                }
            });

            $count++;
        }

        return $count;
    }

    private function findActiveReservationForUserCopy(int $biblioId, int $userId, int $copyId): ?array
    {
        $sql = "
            SELECT * FROM reservation_queues
            WHERE biblio_id = :biblio_id
              AND user_id = :uid
              AND assigned_copy_id = :copy_id
              AND queue_status = 'READY_FOR_PICKUP'
            LIMIT 1
        ";
        $model = new class extends \App\Core\Model {
            public function fetch(string $s, array $p): ?array { return $this->fetchOne($s, $p); }
        };
        return $model->fetch($sql, [':biblio_id' => $biblioId, ':uid' => $userId, ':copy_id' => $copyId]);
    }
}
