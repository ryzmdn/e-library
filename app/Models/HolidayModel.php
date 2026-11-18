<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class HolidayModel extends Model
{
    public function getAllHolidays(): array
    {
        $sql = "SELECT holiday_date, description FROM library_holidays ORDER BY holiday_date ASC";
        return $this->fetchAll($sql);
    }

    public function getHolidaysBetween(string $startDate, string $endDate): array
    {
        $sql = "
            SELECT holiday_date, description
            FROM library_holidays
            WHERE holiday_date BETWEEN :start AND :end
            ORDER BY holiday_date ASC
        ";
        return $this->fetchAll($sql, [':start' => $startDate, ':end' => $endDate]);
    }

    public function isHoliday(string $date): bool
    {
        $sql = "SELECT COUNT(*) FROM library_holidays WHERE holiday_date = :dt";
        return $this->countRows($sql, [':dt' => $date]) > 0;
    }

    public function addHoliday(string $date, string $description): bool
    {
        $sql = "
            INSERT INTO library_holidays (holiday_date, description)
            VALUES (:dt, :desc)
            ON DUPLICATE KEY UPDATE description = VALUES(description)
        ";
        return $this->execute($sql, [':dt' => $date, ':desc' => trim($description)]);
    }

    public function deleteHoliday(string $date): bool
    {
        $sql = "DELETE FROM library_holidays WHERE holiday_date = :dt";
        return $this->execute($sql, [':dt' => $date]);
    }
}
