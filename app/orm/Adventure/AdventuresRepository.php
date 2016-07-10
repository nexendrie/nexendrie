<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class AdventuresRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Adventure::class];
  }
  
  /**
   * @param int $id
   * @return Adventure|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
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