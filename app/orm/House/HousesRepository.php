<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 * @method House|NULL getById($id)
 * @method House|NULL getByOwner($owner)
 */
class HousesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [House::class];
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