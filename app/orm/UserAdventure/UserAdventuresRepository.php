<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method UserAdventure|NULL getById($id)
 * @method ICollection|UserAdventure[] findByUser($user)
 * @method UserAdventure|NULL getLastAdventure($user)
 */
class UserAdventuresRepository extends \Nextras\Orm\Repository\Repository {
  /**
   * Find specified user's active adventure
   * 
   * @param int $user User's id
   * @return UserJob|NULL
   */
  function getUserActiveAdventure($user) {
    return $this->getBy(array("user" => $user, "progress<" => 10));
  }
  
  /**
   * Get specified user's adventures from month
   * 
   * @param int $user
   * @param int $month
   * @param int $year
   * @return ICollection|UserAdventure[]
   */
  function findFromMonth($user, $month = 0, $year = 0) {
    if($month === 0) $month = date("n");
    if($year === 0) $year = date("Y");
    $startOfMonthTS = mktime(0, 0, 0, $month, 1, $year);
    $date = new \DateTime;
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp();
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp();
    return $this->findBy(array("user" => $user, "started>" => $start, "started<" => $end));
  }
  
  /**
   * Get open adventures
   * 
   * @return ICollection|UserAdventure[]
   */
  function findOpenAdventures() {
    $day = date("j");
    $month = date("n");
    $ts = mktime(0, 0, 0, $month, $day);
    return $this->findBy(array("started<" => $ts, "progress<" => 10));
  }
}
?>