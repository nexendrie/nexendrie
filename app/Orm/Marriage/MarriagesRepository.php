<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method ICollection|Marriage[] getActiveMarriage($user)
 * @method ICollection|Marriage[] getAcceptedMarriage($user)
 */
class MarriagesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Marriage::class];
  }
  
  /**
   * @param int $id
   * @return Marriage|NULL
   */
  function getById($id): ?Marriage {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $user1
   * @return ICollection|Marriage[]
   */
  function findByUser1($user1): ICollection {
    return $this->findBy(["user1" => $user1]);
  }
  
  /**
   * @param User|int $user2
   * @return ICollection|Marriage[]
   */
  function findByUser2($user2): ICollection {
    return $this->findBy(["user2" => $user2]);
  }
  
  /**
   * Get proposals for a user
   * 
   * @param int|User $user
   * @return ICollection|Marriage[]
   */
  function findProposals($user): ICollection {
    return $this->findBy([
      "user2" => $user, "status" => Marriage::STATUS_PROPOSED
    ]);
  }
  
  /**
   * Get open weddings
   * 
   * @return ICollection|Marriage[]
   */
  function findOpenWeddings(): ICollection {
    return $this->findBy([
      "status" => Marriage::STATUS_ACCEPTED, "term<=" => time() + 60 * 60
    ]);
  }
}
?>