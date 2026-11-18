<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class MembershipTierModel extends Model
{
    public function getAll(): array
    {
        return $this->fetchAll("SELECT * FROM membership_tiers ORDER BY id ASC");
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne("SELECT * FROM membership_tiers WHERE id = :id LIMIT 1", [':id' => $id]);
    }

    public function findByCode(string $code): ?array
    {
        return $this->fetchOne("SELECT * FROM membership_tiers WHERE tier_code = :code LIMIT 1", [':code' => $code]);
    }

    public function getDefaultTier(): ?array
    {
        $tier = $this->findByCode('REGULER');
        if ($tier) {
            return $tier;
        }
        return $this->fetchOne("SELECT * FROM membership_tiers ORDER BY id ASC LIMIT 1");
    }
}
