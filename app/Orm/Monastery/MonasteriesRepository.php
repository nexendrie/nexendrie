<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class MonasteriesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames(): array {
    return [Monastery::class];
  }
  
  /**
   * @param int $id
   * @return Monastery|NULL
   */
  function getById($id): ?Monastery {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $leader
   * @return Monastery|null
   */
  function getByLeader($leader): ?Monastery {
    return $this->getBy(["leader" => $leader]);
  }
  
  /**
   * @param Town|int $town
   * @return ICollection|Monastery[]
   */
  function findByTown($town): ICollection {
    return $this->findBy(["town" => $town]);
  }
  
  /**
   * @param string $name
   * @return Monastery|null
   */
  function getByName(string $name): ?Monastery {
    return $this->getBy(["name" => $name]);
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