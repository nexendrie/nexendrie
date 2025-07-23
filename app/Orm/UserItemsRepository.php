<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method UserItem|null getById(int $id)
 * @method UserItem|null getBy(array $conds)
 * @method ICollection|UserItem[] findBy(array $conds)
 * @method ICollection|UserItem[] findAll()
 */
final class UserItemsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [UserItem::class];
  }

  public function getByUserAndItem(User|int $user, Item|int $item): ?UserItem {
    return $this->getBy(["user" => $user, "item" => $item]);
  }
  
  /**
   * @return ICollection|UserItem[]
   */
  public function findByUser(User|int $user): ICollection {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * @return ICollection|UserItem[]
   */
  public function findByItem(Item|int $item): ICollection {
    return $this->findBy(["item" => $item]);
  }
  
  /**
   * Find specified user's equipment
   *
   * @return ICollection|UserItem[]
   */
  public function findEquipment(int $user): ICollection {
    return $this->findBy(["user" => $user, "item->type=" => Item::getEquipmentTypes()]);
  }
  
  /**
   * Find specified user's items
   *
   * @return ICollection|UserItem[]
   */
  public function findCommonItems(int $user): ICollection {
    return $this->findBy(["user" => $user, "item->type=" => Item::getCommonTypes()]);
  }
  
  /**
   * Find specified user's items of a type
   *
   * @return ICollection|UserItem[]
   */
  public function findByType(int $user, string $type): ICollection {
    return $this->findBy(["user" => $user, "item->type" => $type]);
  }
  
  /**
   * Get user's active weapon
   */
  public function getWornWeapon(int $user): ?UserItem {
    return $this->getBy(["user" => $user, "item->type" => "weapon", "worn" => true]);
  }
  
  /**
   * Get user's active armor
   */
  public function getWornArmor(int $user): ?UserItem {
    return $this->getBy(["user" => $user, "item->type" => "armor", "worn" => true]);
  }
  
  /**
   * Get user's active helmet
   */
  public function getWornHelmet(int $user): ?UserItem {
    return $this->getBy(["user" => $user, "item->type" => "helmet", "worn" => true]);
  }
}
?>