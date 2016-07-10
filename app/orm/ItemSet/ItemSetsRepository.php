<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class ItemSetsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [ItemSet::class];
  }
  
  /**
   * @param int $id
   * @return ItemSet|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * @param Item|int $weapon
   * @param Item|int $armor
   * @param Item|int $helmet
   * @return ItemSet|NULL
   */
  function getByWeaponAndArmorAndHelmet($weapon,$armor,$helmet) {
    return $this->getBy(array("weapon" => $weapon, "armor" => $armor, "helmet" => $helmet));
  }
}
?>