<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Repository\Repository,
    Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Loan|NULL getById($id)
 * @method ICollection|Loan[] findByUser($user)
 */
class LoansRepository extends Repository {
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
    return $this->findBy(array("user" => $user, "taken>" => $start, "taken<" => $end));
  }
}
?>