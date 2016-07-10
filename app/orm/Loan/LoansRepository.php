<?php
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
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * @param User|int $user
   * @return ICollection|Loan[]
   */
  function findByUser($user) {
    return $this->findBy(array("user" => $user));
  }
  
  /**
   * Get specified user's active loan
   * 
   * @param int $user
   * @return Loan|NULL
   */
  function getActiveLoan($user) {
    return $this->getBy(array("user" => $user, "returned" => NULL));
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
    $startOfMonthTS = mktime(0, 0, 0, $month, 1, $year);
    $date = new \DateTime;
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp();
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp();
    return $this->findBy(array("user" => $user, "returned>" => $start, "returned<" => $end));
  }
}
?>