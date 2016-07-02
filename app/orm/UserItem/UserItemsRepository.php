<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method UserItem|NULL getById($id)
 * @method UserItem|NULL getByUserAndItem($user,$item)
 * @method ICollection|UserItem[] findByUser($user)
 * @method ICollection|UserItem[] findByItem($item)
 */
class UserItemsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [UserItem::class];
  }
  
  /**
   * Find specified user's equipment
   * 
   * @param int $user
   * @return ICollection|UserItem[]
   */
  function findEquipment($user) {
    return $this->findBy(array("user" => $user, "this->item->type=" => Item::getEquipmentTypes()));
  }
  
  /**
   * Find specified user's items
   * 
   * @param int $user
   * @return ICollection|UserItem[]
   */
  function findCommonItems($user) {
    return $this->findBy(array("user" => $user, "this->item->type=" => Item::getCommonTypes()));
  }
  
  /**
   * Find specified user's items of a type
   * 
   * @param int $user
   * @param string $type
   * @return ICollection|UserItem[]
   */
  function findByType($user, $type) {
    return $this->findBy(array("user" => $user, "this->item->type" => $type));
  }
  
  /**
   * Get user's active weapon
   * 
   * @param int $user
   * @return UserItem|NULL
   */
  function getWornWeapon($user) {
    return $this->getBy(array("user" => $user, "this->item->type" => "weapon", "worn" => true));
  }
  
  /**
   * Get user's active armor
   * 
   * @param int $user
   * @return UserItem|NULL
   */
  function getWornArmor($user) {
    return $this->getBy(array("user" => $user, "this->item->type" => "armor", "worn" => true));
  }
  
  /**
   * Get user's active helmet
   * 
   * @param int $user
   * @return UserItem|NULL
   */
  function getWornHelmet($user) {
    return $this->getBy(array("user" => $user, "this->item->type" => "helmet", "worn" => true));
  }
}
?>