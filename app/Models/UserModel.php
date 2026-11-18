<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

final class UserModel extends Model
{
    public function findById(int $id): ?array
    {
        $sql = "
            SELECT u.*, t.tier_name, t.max_physical_loans, t.loan_duration_days, t.daily_fine_amount
            FROM users u
            INNER JOIN membership_tiers t ON u.tier_id = t.id
            WHERE u.id = :id
            LIMIT 1
        ";
        return $this->fetchOne($sql, [':id' => $id]);
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "
            SELECT u.*, t.tier_name, t.max_physical_loans, t.loan_duration_days, t.daily_fine_amount
            FROM users u
            INNER JOIN membership_tiers t ON u.tier_id = t.id
            WHERE u.email = :email
            LIMIT 1
        ";
        return $this->fetchOne($sql, [':email' => strtolower(trim($email))]);
    }

    public function findByMemberNumber(string $memberNumber): ?array
    {
        $sql = "
            SELECT u.*, t.tier_name, t.max_physical_loans, t.loan_duration_days, t.daily_fine_amount
            FROM users u
            INNER JOIN membership_tiers t ON u.tier_id = t.id
            WHERE u.member_number = :member_number
            LIMIT 1
        ";
        return $this->fetchOne($sql, [':member_number' => trim($memberNumber)]);
    }

    public function findByIdentifier(string $identifier): ?array
    {
        $identifier = trim($identifier);
        $sql = "
            SELECT u.*, t.tier_name, t.max_physical_loans, t.loan_duration_days, t.daily_fine_amount
            FROM users u
            INNER JOIN membership_tiers t ON u.tier_id = t.id
            WHERE u.email = :email OR u.member_number = :member_number
            LIMIT 1
        ";
        return $this->fetchOne($sql, [
            ':email' => strtolower($identifier),
            ':member_number' => $identifier,
        ]);
    }

    public function isEmailTaken(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $params = [':email' => strtolower(trim($email))];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        return $this->countRows($sql, $params) > 0;
    }

    public function generateMemberNumber(): string
    {
        $year = date('Y');
        $prefix = 'M-' . $year . '-';

        $sql = "SELECT member_number FROM users WHERE member_number LIKE :prefix ORDER BY id DESC LIMIT 1";
        $latest = $this->fetchOne($sql, [':prefix' => $prefix . '%']);

        if ($latest && preg_match('/-(\d+)$/', $latest['member_number'], $matches)) {
            $nextSequence = (int)$matches[1] + 1;
        } else {
            $nextSequence = 1;
        }

        return $prefix . str_pad((string)$nextSequence, 4, '0', STR_PAD_LEFT);
    }

    public function createMember(array $data): int
    {
        $memberNumber = $this->generateMemberNumber();
        $membershipExpiresAt = date('Y-m-d', strtotime('+3 years'));

        $sql = "
            INSERT INTO users (
                member_number, email, password_hash, full_name, phone_number,
                role, tier_id, account_status, theme_preference, font_scale_percent,
                membership_expires_at
            ) VALUES (
                :member_number, :email, :password_hash, :full_name, :phone_number,
                'MEMBER', :tier_id, 'ACTIVE', 'LIGHT', 100,
                :membership_expires_at
            )
        ";

        $this->execute($sql, [
            ':member_number' => $memberNumber,
            ':email' => strtolower(trim($data['email'])),
            ':password_hash' => $data['password_hash'],
            ':full_name' => trim($data['full_name']),
            ':phone_number' => isset($data['phone_number']) && $data['phone_number'] !== '' ? trim($data['phone_number']) : null,
            ':tier_id' => $data['tier_id'] ?? 1,
            ':membership_expires_at' => $membershipExpiresAt,
        ]);

        return (int)$this->lastInsertId();
    }

    public function recordFailedLogin(int $userId): void
    {
        $user = $this->findById($userId);
        if (!$user) {
            return;
        }

        $attempts = (int)$user['failed_login_attempts'] + 1;
        $lockedUntil = null;

        if ($attempts >= 5) {
            $lockedUntil = date('Y-m-d H:i:s', time() + (15 * 60));
        }

        $sql = "
            UPDATE users
            SET failed_login_attempts = :attempts, locked_until = :locked_until
            WHERE id = :id
        ";
        $this->execute($sql, [
            ':attempts' => $attempts,
            ':locked_until' => $lockedUntil,
            ':id' => $userId,
        ]);
    }

    public function resetFailedLogins(int $userId): void
    {
        $sql = "
            UPDATE users
            SET failed_login_attempts = 0, locked_until = NULL
            WHERE id = :id
        ";
        $this->execute($sql, [':id' => $userId]);
    }

    public function updateProfile(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        if (isset($data['full_name'])) {
            $fields[] = "full_name = :full_name";
            $params[':full_name'] = trim($data['full_name']);
        }

        if (array_key_exists('phone_number', $data)) {
            $fields[] = "phone_number = :phone_number";
            $params[':phone_number'] = $data['phone_number'] !== null && $data['phone_number'] !== '' ? trim($data['phone_number']) : null;
        }

        if (isset($data['theme_preference'])) {
            $fields[] = "theme_preference = :theme_preference";
            $params[':theme_preference'] = $data['theme_preference'];
        }

        if (isset($data['font_scale_percent'])) {
            $fields[] = "font_scale_percent = :font_scale_percent";
            $params[':font_scale_percent'] = (int)$data['font_scale_percent'];
        }

        if (empty($fields)) {
            return true;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        return $this->execute($sql, $params);
    }

    public function updatePassword(int $id, string $passwordHash): bool
    {
        $sql = "UPDATE users SET password_hash = :hash WHERE id = :id";
        return $this->execute($sql, [
            ':hash' => $passwordHash,
            ':id' => $id,
        ]);
    }
}
