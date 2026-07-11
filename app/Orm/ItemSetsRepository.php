<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<ItemSet>
 */
final class ItemSetsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [ItemSet::class];
    }

    public function getByWeaponAndArmorAndHelmet(
        Item|int|null $weapon,
        Item|int|null $armor,
        Item|int|null $helmet
    ): ?ItemSet {
        return $this->getBy(["weapon" => $weapon, "armor" => $armor, "helmet" => $helmet]);
    }
}
