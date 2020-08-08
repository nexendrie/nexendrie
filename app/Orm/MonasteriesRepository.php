<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class MonasteriesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Monastery::class];
  }
  
  /**
   * @param int $id
   * @return Monastery|null
   */
  public function getById($id): ?\Nextras\Orm\Entity\IEntity {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $leader
   */
  public function getByLeader($leader): ?Monastery {
    return $this->getBy(["leader" => $leader]);
  }
  
  /**
   * @param Town|int $town
   * @return ICollection|Monastery[]
   */
  public function findByTown($town): ICollection {
    return $this->findBy(["town" => $town]);
  }
  
  public function getByName(string $name): ?Monastery {
    return $this->getBy(["name" => $name]);
  }
  
  /**
   * Get monasteries led by users
   * 
   * @return ICollection|Monastery[]
   */
  public function findLedMonasteries(): ICollection {
    return $this->findBy(["this->leader->id>" => 0]);
  }
}
?>