<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method ItemSet|null getById(int $id)
 * @method ItemSet|null getBy(array $conds)
 * @method ICollection|ItemSet[] findBy(array $conds)
 * @method ICollection|ItemSet[] findAll()
 */
final class ItemSetsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [ItemSet::class];
  }

  public function getByWeaponAndArmorAndHelmet(Item|int|null $weapon, Item|int|null $armor, Item|int|null $helmet): ?ItemSet {
    return $this->getBy(["weapon" => $weapon, "armor" => $armor, "helmet" => $helmet]);
  }
}
?>