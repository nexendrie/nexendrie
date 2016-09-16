<?php
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
  function findLedMonasteries() {
    return $this->findBy(["this->owner->id>" => 0]);
  }
}
?>