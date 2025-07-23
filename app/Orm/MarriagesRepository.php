<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Marriage|null getById(int $id)
 * @method Marriage|null getBy(array $conds)
 * @method ICollection|Marriage[] findBy(array $conds)
 * @method ICollection|Marriage[] findAll()
 */
final class MarriagesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Marriage::class];
  }
  
  /**
   * @return ICollection|Marriage[]
   */
  public function findByUser1(User|int $user1): ICollection {
    return $this->findBy(["user1" => $user1]);
  }
  
  /**
   * @return ICollection|Marriage[]
   */
  public function findByUser2(User|int $user2): ICollection {
    return $this->findBy(["user2" => $user2]);
  }
  
  /**
   * Get proposals for a user
   *
   * @return ICollection|Marriage[]
   */
  public function findProposals(User|int $user): ICollection {
    return $this->findBy([
      "user2" => $user, "status" => Marriage::STATUS_PROPOSED
    ]);
  }
  
  /**
   * Get open weddings
   * 
   * @return ICollection|Marriage[]
   */
  public function findOpenWeddings(): ICollection {
    return $this->findBy([
      "status" => Marriage::STATUS_ACCEPTED, "term<=" => time() + 60 * 60
    ]);
  }

  public function getActiveMarriage(User|int $user): ?Marriage {
    return $this->getBy([
      ICollection::OR,
      [
        "status" => Marriage::STATUS_ACTIVE,
        "user1" => $user
      ],
      [
        "status" => Marriage::STATUS_ACTIVE,
        "user2" => $user
      ]
    ]);
  }

  public function getAcceptedMarriage(User|int $user): ?Marriage {
    return $this->getBy([
      ICollection::OR,
      [
        "status" => Marriage::STATUS_ACCEPTED,
        "user1" => $user
      ],
      [
        "status" => Marriage::STATUS_ACCEPTED,
        "user2" => $user
      ]
    ]);
  }
}
?>