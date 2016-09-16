<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class UserItemsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [UserItem::class];
  }
  
  /**
   * @param int $id
   * @return UserItem|NULL
   */
  function getById($id) {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $user
   * @param Item|int $item
   * @return UserItem|NULL
   */
  function getByUserAndItem($user, $item) {
    return $this->getBy(["user" => $user, "item" => $item]);
  }
  
  /**
   * @param User|int $user
   * @return ICollection|UserItem[]
   */
  function findByUser($user) {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * @param Item|int $item
   * @return ICollection|UserItem[]
   */
  function findByItem($item) {
    return $this->findBy(["item" => $item]);
  }
  
  /**
   * Find specified user's equipment
   * 
   * @param int $user
   * @return ICollection|UserItem[]
   */
  function findEquipment($user) {
    return $this->findBy(["user" => $user, "this->item->type=" => Item::getEquipmentTypes()]);
  }
  
  /**
   * Find specified user's items
   * 
   * @param int $user
   * @return ICollection|UserItem[]
   */
  function findCommonItems($user) {
    return $this->findBy(["user" => $user, "this->item->type=" => Item::getCommonTypes()]);
  }
  
  /**
   * Find specified user's items of a type
   * 
   * @param int $user
   * @param string $type
   * @return ICollection|UserItem[]
   */
  function findByType($user, $type) {
    return $this->findBy(["user" => $user, "this->item->type" => $type]);
  }
  
  /**
   * Get user's active weapon
   * 
   * @param int $user
   * @return UserItem|NULL
   */
  function getWornWeapon($user) {
    return $this->getBy(["user" => $user, "this->item->type" => "weapon", "worn" => true]);
  }
  
  /**
   * Get user's active armor
   * 
   * @param int $user
   * @return UserItem|NULL
   */
  function getWornArmor($user) {
    return $this->getBy(["user" => $user, "this->item->type" => "armor", "worn" => true]);
  }
  
  /**
   * Get user's active helmet
   * 
   * @param int $user
   * @return UserItem|NULL
   */
  function getWornHelmet($user) {
    return $this->getBy(["user" => $user, "this->item->type" => "helmet", "worn" => true]);
  }
}
?>