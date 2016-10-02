<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class LoansRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Loan::class];
  }
  
  /**
   * @param int $id
   * @return Loan|NULL
   */
  function getById($id) {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $user
   * @return ICollection|Loan[]
   */
  function findByUser($user) {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * Get specified user's active loan
   * 
   * @param int $user
   * @return Loan|NULL
   */
  function getActiveLoan($user) {
    return $this->getBy(["user" => $user, "returned" => NULL]);
  }
  
  /**
   * Get loans returned this month by specified user
   * 
   * @param int $user
   * @return ICollection|Loan[]
   */
  function findReturnedThisMonth($user) {
    $month = date("n");
    $year = date("Y");
    $startOfMonthTS = mktime(0, 0, 0, (int) $month, 1, (int) $year);
    $date = new \DateTime;
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp();
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp();
    return $this->findBy(["user" => $user, "returned>" => $start, "returned<" => $end]);
  }
}
?>