<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class PunishmentsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Punishment::class];
  }
  
  /**
   * @param int $id
   * @return Punishment|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * @param User|int $user
   * @return ICollection|Punishment
   */
  function findByUser($user) {
    return $this->findBy(array("user" => $user));
  }
  
  /**
   * Find specified user's active punishment
   * 
   * @param int $user User's id
   * @return Punishment|NULL
   */
  function getActivePunishment($user){
    return $this->getBy(array("user" => $user, "released" => NULL));
  }
}
?>