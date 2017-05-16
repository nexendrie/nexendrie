<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class JobsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Job::class];
  }
  
  /**
   * @param int $id
   * @return Job|NULL
   */
  function getById($id): ?Job {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * Find jobs for specified level
   * 
   * @param int $level
   * @return ICollection|Job[]
   */
  function findForLevel(int $level): ICollection {
    return $this->findBy(["level<=" => $level]);
  }
}
?>