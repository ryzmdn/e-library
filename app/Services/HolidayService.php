<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\HolidayModel;
use DateTimeImmutable;

final class HolidayService
{
    private HolidayModel $holidayModel;
    private array $holidayCache = [];

    public function __construct(?HolidayModel $holidayModel = null)
    {
        $this->holidayModel = $holidayModel ?? new HolidayModel();
        $this->loadHolidays();
    }

    private function loadHolidays(): void
    {
        try {
            $rows = $this->holidayModel->getAllHolidays();
            foreach ($rows as $row) {
                $this->holidayCache[$row['holiday_date']] = $row['description'];
            }
        } catch (\Throwable $e) {
            $this->holidayCache = [];
        }
    }

    public function isHoliday(string $date): bool
    {
        return isset($this->holidayCache[$date]);
    }

    public function isSunday(string $date): bool
    {
        $dt = new DateTimeImmutable($date);
        return (int)$dt->format('N') === 7;
    }

    public function isClosedDay(string $date): bool
    {
        return $this->isSunday($date) || $this->isHoliday($date);
    }

    public function calculateDueDate(string $startDate, int $durationDays): string
    {
        $dt = new DateTimeImmutable($startDate);
        $target = $dt->modify("+{$durationDays} days");

        while ($this->isClosedDay($target->format('Y-m-d'))) {
            $target = $target->modify('+1 day');
        }

        return $target->format('Y-m-d');
    }

    public function calculateOverdueDays(string $dueDate, string $returnDate): int
    {
        $due = new DateTimeImmutable($dueDate);
        $ret = new DateTimeImmutable($returnDate);

        if ($ret <= $due) {
            return 0;
        }

        $overdueDays = 0;
        $current = $due->modify('+1 day');

        while ($current <= $ret) {
            $dateStr = $current->format('Y-m-d');
            if (!$this->isClosedDay($dateStr)) {
                $overdueDays++;
            }
            $current = $current->modify('+1 day');
        }

        return $overdueDays;
    }

    public function getNextOpenDay(string $date): string
    {
        $target = new DateTimeImmutable($date);
        while ($this->isClosedDay($target->format('Y-m-d'))) {
            $target = $target->modify('+1 day');
        }
        return $target->format('Y-m-d');
    }
}
