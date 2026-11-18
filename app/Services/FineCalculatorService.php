<?php
declare(strict_types=1);

namespace App\Services;

final class FineCalculatorService
{
    public function calculate(int $overdueDays, float $dailyRate, float $cap): float
    {
        if ($overdueDays <= 0 || $dailyRate <= 0.0) {
            return 0.00;
        }

        $rawFine = $overdueDays * $dailyRate;
        if ($cap > 0.0 && $rawFine > $cap) {
            return round($cap, 2);
        }

        return round($rawFine, 2);
    }

    public function assessFineForLoan(array $loan, array $tier, string $returnDate, HolidayService $holidayService): float
    {
        $dueDate = (string)($loan['due_date'] ?? date('Y-m-d'));
        $dailyRate = (float)($tier['daily_fine_amount'] ?? 1000.00);
        $cap = (float)($tier['max_fine_cap'] ?? 50000.00);

        $overdueDays = $holidayService->calculateOverdueDays($dueDate, $returnDate);
        return $this->calculate($overdueDays, $dailyRate, $cap);
    }
}
