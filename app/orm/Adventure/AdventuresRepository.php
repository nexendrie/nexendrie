<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Repository\Repository,
    Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * 
 * @method Adventure|NULL getById($id)
 */
class AdventuresRepository extends Repository {
  /**
   * Find adventures for specified level
   * 
   * @param int $level
   * @return ICollection|Adventure[]
   */
  function findForLevel($level) {
    return $this->findBy(array("level<=" => $level));
  }
}
?>