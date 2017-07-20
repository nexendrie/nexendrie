<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class JobsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames(): array {
    return [Job::class];
  }
  
  /**
   * @param int $id
   * @return Job|NULL
   */
  public function getById($id): ?Job {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * Find jobs for specified level
   * 
   * @param int $level
   * @return ICollection|Job[]
   */
  public function findForLevel(int $level): ICollection {
    return $this->findBy(["level<=" => $level]);
  }
}
?>