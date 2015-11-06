<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Job|NULL getById($id)
 */
class JobsRepository extends \Nextras\Orm\Repository\Repository {
  /**
   * Find jobs for specified level
   * 
   * @param int $level
   * @return ICollection|Job[]
   */
  function findForLevel($level) {
    return $this->findBy(array("level<=" => $level));
  }
}
?>