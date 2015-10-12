<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Repository\Repository,
    Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method UserJob|NULL getById($id)
 * @method ICollection|UserJob[] findByUser($user)
 */
class UserJobsRepository extends Repository {
  /**
   * Find specified user's active job
   * 
   * @param int $user User's id
   * @return UserJob|NULL
   */
  function getUserActiveJob($user) {
    return $this->getBy(array("user" => $user, "finished" => 0));
  }
}
?>