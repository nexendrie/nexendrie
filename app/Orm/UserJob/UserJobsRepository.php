<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class UserJobsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [UserJob::class];
  }
  
  /**
   * @param int $id
   * @return UserJob|NULL
   */
  function getById($id): ?UserJob {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $user
   * @return ICollection|UserJob[]
   */
  function findByUser($user): ICollection {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * Find specified user's active job
   * 
   * @param int $user User's id
   * @return UserJob|NULL
   */
  function getUserActiveJob(int $user) {
    return $this->getBy(["user" => $user, "finished" => false]);
  }
  
  /**
   * Get specified user's jobs from month
   * 
   * @param int $user
   * @param int $month
   * @param int $year
   * @return ICollection|UserJob[]
   */
  function findFromMonth(int $user, int $month = 0, int $year = 0) {
    $sixDays = 60 * 60 * 24 * 6;
    $startOfMonthTS = mktime(0, 0, 0, $month, 1, $year);
    $date = new \DateTime;
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp() - $sixDays;
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp() - $sixDays;
    return $this->findBy(["user" => $user, "started>" => $start, "started<" => $end]);
  }
}
?>