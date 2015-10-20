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
    return $this->getBy(array("user" => $user, "finished" => false));
  }
  
  /**
   * Get specified user's jobs from current month
   * 
   * @param int $user
   * @return ICollection|UserJob[]
   */
  function findFromThisMonth($user) {
    $sixDays = 60 * 60 * 24 * 6;
    $month = date("n");
    $year = date("Y");
    $startOfMonthTS = mktime(0, 0, 0, $month, 1, $year);
    $date = new \DateTime;
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp() - $sixDays;
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp() + $sixDays;
    return $this->findBy(array("user" => $user, "started>" => $start, "started<" => $end));
  }
}
?>