<?php
declare(strict_types=1);

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
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * Find adventures for specified level
   * 
   * @param int $level
   * @return ICollection|Adventure[]
   */
  function findForLevel(int $level): ICollection {
    return $this->findBy(["level<=" => $level]);
  }
}
?>