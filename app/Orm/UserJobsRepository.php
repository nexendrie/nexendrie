<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method UserJob|null getById(int $id)
 * @method UserJob|null getBy(array $conds)
 * @method ICollection|UserJob[] findBy(array $conds)
 * @method ICollection|UserJob[] findAll()
 */
final class UserJobsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [UserJob::class];
  }
  
  /**
   * @param User|int $user
   * @return ICollection|UserJob[]
   */
  public function findByUser($user): ICollection {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * Find specified user's active job
   */
  public function getUserActiveJob(int $user): ?UserJob {
    return $this->getBy(["user" => $user, "finished" => false]);
  }
  
  /**
   * Get specified user's jobs from month
   *
   * @return ICollection|UserJob[]
   */
  public function findFromMonth(int $user, int $month = 0, int $year = 0): ICollection {
    $sixDays = 60 * 60 * 24 * 6;
    $startOfMonthTS = mktime(0, 0, 0, $month, 1, $year);
    $date = new \DateTime();
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp() - $sixDays;
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp() - $sixDays;
    return $this->findBy(["user" => $user, "created>" => $start, "created<" => $end]);
  }
}
?>