<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Marriage|NULL getActiveMarriage($user)
 * @method Marriage|NULL getAcceptedMarriage($user)
 */
class MarriagesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Marriage::class];
  }
  
  /**
   * @param int $id
   * @return Marriage|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * @param User|int $user1
   * @return ICollection|Marriage[]
   */
  function findByUser1($user1) {
    return $this->findBy(array("user1" => $user1));
  }
  
  /**
   * @param User|int $user2
   * @return ICollection|Marriage[]
   */
  function findByUser2($user2) {
    return $this->findBy(array("user2" => $user2));
  }
  
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
  
  /**
   * Get open weddings
   * 
   * @return ICollection|Marriage[]
   */
  function findOpenWeddings() {
    return $this->findBy(array(
      "status" => Marriage::STATUS_ACCEPTED, "term<=" => time() + 60 * 60
    ));
  }
}
?>