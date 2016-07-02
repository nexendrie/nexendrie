<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Castle|NULL getById($id)
 * @method Castle|NULL getByOwner($owner)
 * @method Castle|NULL getByName($name)
 */
class CastlesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Castle::class];
  }
  
  /**
   * Get castles owned by users
   * 
   * @return ICollection|Castle[]
   */
  function findOwnedCastles() {
    return $this->findBy(array("this->owner->id>" => 0));
  }
}
?>