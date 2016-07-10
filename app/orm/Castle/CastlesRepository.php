<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class CastlesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Castle::class];
  }
  
  /**
   * @param int $id
   * @return Castle|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * @param User|int $owner
   * @return Castle|NULL
   */
  function getByOwner($owner) {
    return $this->getBy(array("owner" => $owner));
  }
  
  /**
   * @param string $name
   * @return Castle|NULL
   */
  function getByName($name) {
    return $this->getBy(array("name" => $name));
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