<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Marriage|NULL getById($id)
 * @method ICollection|Marriage[] findByUser1($user1)
 * @method ICollection|Marriage[] findByUser2($user2)
 * @method Marriage|NULL getActiveMarriage($user)
 * @method Marriage|NULL getAcceptedMarriage($user)
 */
class MarriagesRepository extends \Nextras\Orm\Repository\Repository {
  /**
   * Get proposals for a user
   * 
   * @param int|User $user
   * @return ICollection|Marriage[]
   */
  function findProposals($user) {
    return $this->findBy(array(
      "user2" => $user, "status" => Marriage::STATUS_PROPOSED
    ));
  }
}
?>