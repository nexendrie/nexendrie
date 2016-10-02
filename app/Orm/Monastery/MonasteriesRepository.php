<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Monastery|NULL getById($id)
 * @method Monastery|NULL getByLeader($leader)
 * @method ICollection|Monastery[] findByTown($town)
 * @method Monastery|NULL getByName($name)
 */
class MonasteriesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Monastery::class];
  }
  
  /**
   * Get monasteries led by users
   * 
   * @return ICollection|Monastery[]
   */
  function findLedMonasteries(): ICollection {
    return $this->findBy(["this->leader->id>" => 0]);
  }
}
?>