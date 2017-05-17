<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class ItemSetsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames(): array {
    return [ItemSet::class];
  }
  
  /**
   * @param int $id
   * @return ItemSet|NULL
   */
  function getById($id): ?ItemSet {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param Item|int $weapon
   * @param Item|int $armor
   * @param Item|int $helmet
   * @return ItemSet|NULL
   */
  function getByWeaponAndArmorAndHelmet($weapon, $armor, $helmet): ?ItemSet {
    return $this->getBy(["weapon" => $weapon, "armor" => $armor, "helmet" => $helmet]);
  }
}
?>