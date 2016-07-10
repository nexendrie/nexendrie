<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class HousesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [House::class];
  }
  
  /**
   * @param int $id
   * @return House|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * @param User|int $owner
   * @return House|NULL
   */
  function getByOwner($owner) {
    return $this->getBy(array("owner" => $owner));
  }
  
  /**
   * Get houses owned by users
   * 
   * @return ICollection|House[]
   */
  function findOwnedHouses() {
    return $this->findBy(array("this->owner->id>" => 0));
  }
}
?>