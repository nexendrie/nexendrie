<?php
declare(strict_types=1);

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
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $owner
   * @return ICollection|Mount[]
   */
  function findByOwner($owner) {
    return $this->findBy(["owner" => $owner]);
  }
  
  
  /**
   * @param MountType|int $type
   * @return ICollection|Mount[]
   */
  function findByType($type) {
    return $this->findBy(["type" => $type]);
  }
  /**
   * Get mounts on market
   * 
   * @return ICollection|Mount[]
   */
  function findOnMarket() {
    return $this->findBy(["onMarket" => true]);
  }
  
  /**
   * Get mounts owned by users
   * 
   * @return ICollection|Mount[]
   */
  function findOwnedMounts() {
    return $this->findBy(["this->owner->id>" => 0]);
  }
  
  /**
   * Get mounts of specified user for adventure
   * 
   * @param int $user
   * @return ICollection|Mount[]
   */
  function findGoodMounts($user) {
    return $this->findBy(["this->owner->id" => $user, "hp>" => 30]);
  }
}
?>