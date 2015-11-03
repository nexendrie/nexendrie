<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Repository\Repository,
    Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Mount|NULL getById($id)
 * @method ICollection|Mount[] findByOwner($owner)
 * @method ICollection|Mount[] findByType($type)
 */
class MountsRepository extends Repository {
  /**
   * Get mounts on market
   * 
   * @return ICollection|Mount[]
   */
  function findOnMarket() {
    return $this->findBy(array("onMarket" => true));
  }
  
  /**
   * Get mounts owned by users
   * 
   * @return ICollection|Mount[]
   */
  function findOwnedMounts() {
    return $this->findBy(array("this->owner->id>" => 0));
  }
  
  /**
   * Get mounts of specified user for adventure
   * 
   * @param type $user
   * @return ICollection|Mount[]
   */
  function findGoodMounts($user) {
    return $this->findBy(array("this->owner->id" => $user, "hp>" => 30));
  }
}
?>