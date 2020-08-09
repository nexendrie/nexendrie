<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class AdventureNpcsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [AdventureNpc::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?AdventureNpc {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * 
   * @param Adventure|int $adventure
   * @param Order|int $order
   */
  public function getByAdventureAndOrder($adventure, $order): ?AdventureNpc {
    return $this->getBy(["adventure" => $adventure, "order" => $order]);
  }
  
  /**
   * @return ICollection|AdventureNpc[]
   */
  public function findByAdventure(int $adventure): ICollection {
    return $this->findBy(["adventure" => $adventure])->orderBy("order");
  }
}
?>