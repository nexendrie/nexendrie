<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
final class ItemSetsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [ItemSet::class];
  }
  
  /**
   * @param int $id
   * @return ItemSet|null
   */
  public function getById($id): ?\Nextras\Orm\Entity\IEntity {
    return $this->getBy(["id" => $id]);
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