<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class MountsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Mount::class];
  }
  
  /**
   * @param int $id
   * @return Mount|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * @param User|int $owner
   * @return ICollection|Mount[]
   */
  function findByOwner($owner) {
    return $this->findBy(array("owner" => $owner));
  }
  
  
  /**
   * @param MountType|int $type
   * @return ICollection|Mount[]
   */
  function findByType($type) {
    return $this->findBy(array("type" => $type));
  }
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
   * @param int $user
   * @return ICollection|Mount[]
   */
  function findGoodMounts($user) {
    return $this->findBy(array("this->owner->id" => $user, "hp>" => 30));
  }
}
?>