<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * DepositsRepository
 *
 * @author Jakub Konečný
 */
class DepositsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Deposit::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Deposit {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $user
   * @return ICollection|Deposit[]
   */
  public function findByUser($user): ICollection {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * Get specified user's active loan
   */
  public function getActiveDeposit(int $user): ?Deposit {
    return $this->getBy(["user" => $user, "closed" => false]);
  }
}
?>