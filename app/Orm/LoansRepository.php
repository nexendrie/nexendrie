<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class LoansRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Loan::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Loan {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $user
   * @return ICollection|Loan[]
   */
  public function findByUser($user): ICollection {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * Get specified user's active loan
   */
  public function getActiveLoan(int $user): ?Loan {
    return $this->getBy(["user" => $user, "returned" => null]);
  }
  
  /**
   * Get loans returned this month by specified user
   *
   * @return ICollection|Loan[]
   */
  public function findReturnedThisMonth(int $user): ICollection {
    $month = (int) date("n");
    $year = (int) date("Y");
    $startOfMonthTS = mktime(0, 0, 0, $month, 1, $year);
    $date = new \DateTime();
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp();
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp();
    return $this->findBy(["user" => $user, "returned>" => $start, "returned<" => $end]);
  }
}
?>