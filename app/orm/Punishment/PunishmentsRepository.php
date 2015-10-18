<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Repository\Repository,
    Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Punishment|NULL getById($id)
 * @method ICollection|Punishment findByUser($user)
 */
class PunishmentsRepository extends Repository {
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