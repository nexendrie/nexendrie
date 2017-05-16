<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class AdventureNpcsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [AdventureNpc::class];
  }
  
  /**
   * @param int $id
   * @return AdventureNpc|NULL
   */
  function getById($id): ?AdventureNpc {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * 
   * @param Adventure|int $adventure
   * @param Order|int $order
   * @return AdventureNPC|NULL
   */
  function getByAdventureAndOrder($adventure, $order): ?AdventureNpc {
    return $this->getBy(["adventure" => $adventure, "order" => $order]);
  }
  
  /**
   * Get npcs from specified adventure
   * 
   * @param int $adventure
   * @return ICollection|AdventureNpc[]
   */
  function findByAdventure(int $adventure): ICollection {
    return $this->findBy(["adventure" => $adventure])->orderBy("order");
  }
}
?>