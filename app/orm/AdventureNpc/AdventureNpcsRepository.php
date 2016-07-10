<?php
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
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * 
   * @param Adventure|int $adventure
   * @param Order|int $order
   * @return AdventureNPC|NULL
   */
  function getByAdventureAndOrder($adventure, $order) {
    return $this->getBy(array("adventure" => $adventure, "order" => $order));
  }
  
  /**
   * Get npcs from specified adventure
   * 
   * @param int $adventure
   * @return ICollection|AdventureNpc[]
   */
  function findByAdventure($adventure) {
    return $this->findBy(array("adventure" => $adventure))->orderBy("order");
  }
}
?>