<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method UserAdventure|NULL getLastAdventure($user)
 */
class UserAdventuresRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [UserAdventure::class];
  }
  
  /**
   * @param int $id
   * @return UserAdventure|NULL
   */
  function getById($id) {
    return $this->getBy(["id" => $id]);
  }
  
  
  /**
   * @param User|int $user
   * @return ICollection|UserAdventure[]
   */
  function findByUser($user): ICollection {
    return $this->findBy(["user" => $user]);
  }
  /**
   * Find specified user's active adventure
   * 
   * @param int $user User's id
   * @return UserAdventure|NULL
   */
  function getUserActiveAdventure(int $user) {
    return $this->getBy(["user" => $user, "progress<" => 10]);
  }
  
  /**
   * Get specified user's adventures from month
   * 
   * @param int $user
   * @param int $month
   * @param int $year
   * @return ICollection|UserAdventure[]
   */
  function findFromMonth(int $user, int $month = NULL, int $year = NULL) {
    $startOfMonthTS = mktime(0, 0, 0, $month ?? (int) date("n"), 1, $year ?? (int) date("Y"));
    $date = new \DateTime;
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp();
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp();
    return $this->findBy(["user" => $user, "started>" => $start, "started<" => $end]);
  }
  
  /**
   * Get open adventures
   * 
   * @return ICollection|UserAdventure[]
   */
  function findOpenAdventures(): ICollection {
    $day = date("j");
    $month = date("n");
    $ts = mktime(0, 0, 0, (int) $month, (int) $day);
    return $this->findBy(["started<" => $ts, "progress<" => 10]);
  }
}
?>