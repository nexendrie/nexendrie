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
  
  /**
   * @param Item|int $weapon
   * @param Item|int $armor
   * @param Item|int $helmet
   */
  public function getByWeaponAndArmorAndHelmet($weapon, $armor, $helmet): ?ItemSet {
    return $this->getBy(["weapon" => $weapon, "armor" => $armor, "helmet" => $helmet]);
  }
}
?>