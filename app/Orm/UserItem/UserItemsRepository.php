<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class UserItemsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames(): array {
    return [UserItem::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?UserItem {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $user
   * @param Item|int $item
   */
  public function getByUserAndItem($user, $item): ?UserItem {
    return $this->getBy(["user" => $user, "item" => $item]);
  }
  
  /**
   * @param User|int $user
   * @return ICollection|UserItem[]
   */
  public function findByUser($user): ICollection {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * @param Item|int $item
   * @return ICollection|UserItem[]
   */
  public function findByItem($item): ICollection {
    return $this->findBy(["item" => $item]);
  }
  
  /**
   * Find specified user's equipment
   *
   * @return ICollection|UserItem[]
   */
  public function findEquipment(int $user): ICollection {
    return $this->findBy(["user" => $user, "this->item->type=" => Item::getEquipmentTypes()]);
  }
  
  /**
   * Find specified user's items
   *
   * @return ICollection|UserItem[]
   */
  public function findCommonItems(int $user): ICollection {
    return $this->findBy(["user" => $user, "this->item->type=" => Item::getCommonTypes()]);
  }
  
  /**
   * Find specified user's items of a type
   *
   * @return ICollection|UserItem[]
   */
  public function findByType(int $user, string $type): ICollection {
    return $this->findBy(["user" => $user, "this->item->type" => $type]);
  }
  
  /**
   * Get user's active weapon
   */
  public function getWornWeapon(int $user): ?UserItem {
    return $this->getBy(["user" => $user, "this->item->type" => "weapon", "worn" => true]);
  }
  
  /**
   * Get user's active armor
   */
  public function getWornArmor(int $user): ?UserItem {
    return $this->getBy(["user" => $user, "this->item->type" => "armor", "worn" => true]);
  }
  
  /**
   * Get user's active helmet
   */
  public function getWornHelmet(int $user): ?UserItem {
    return $this->getBy(["user" => $user, "this->item->type" => "helmet", "worn" => true]);
  }
}
?>