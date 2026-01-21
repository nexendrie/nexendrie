<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;
use Nexendrie\Utils\Numbers;

/**
 * OrderRank
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property int $adventureBonus
 * @property int $orderFee
 * @property int $created
 * @property int $updated
 * @property OneHasMany|User[] $people {1:m User::$orderRank}
 */
final class OrderRank extends BaseEntity
{
    protected function setterIncomeBonus(int $value): int
    {
        return Numbers::clamp($value, 0, 99);
    }

    protected function setterGuildFee(int $value): int
    {
        return Numbers::clamp($value, 0, 999);
    }
}
