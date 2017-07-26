<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class TownsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames(): array {
    return [Town::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Town {
    return $this->getBy(["id" => $id]);
  }
  
  public function getByName(string $name): ?Town {
    return $this->getBy(["name" => $name]);
  }
  
  /**
   * @param User|int $owner
   * @return ICollection|Town[]
   */
  public function findByOwner($owner): ICollection {
    return $this->findBy(["owner" => $owner]);
  }
  
  /**
   * Get towns on sale
   * 
   * @return ICollection|Town[]
   */
  public function findOnMarket(): ICollection {
    return $this->findBy(["onMarket" => true]);
  }
}
?>