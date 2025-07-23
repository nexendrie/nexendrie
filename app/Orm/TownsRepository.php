<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Town|null getById(int $id)
 * @method Town|null getBy(array $conds)
 * @method ICollection|Town[] findBy(array $conds)
 * @method ICollection|Town[] findAll()
 */
final class TownsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Town::class];
  }
  
  public function getByName(string $name): ?Town {
    return $this->getBy(["name" => $name]);
  }
  
  /**
   * @return ICollection|Town[]
   */
  public function findByOwner(User|int $owner): ICollection {
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