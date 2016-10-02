<?php
declare(strict_types=1);

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
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $user
   * @return ICollection|Punishment
   */
  function findByUser($user) {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * Find specified user's active punishment
   * 
   * @param int $user User's id
   * @return Punishment|NULL
   */
  function getActivePunishment($user) {
    return $this->getBy(["user" => $user, "released" => NULL]);
  }
}
?>