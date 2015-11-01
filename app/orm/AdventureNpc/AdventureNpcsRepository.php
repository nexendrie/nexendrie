<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Repository\Repository,
    Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method AdventureNpc|NULL getById($id)
 */
class AdventureNpcsRepository extends Repository {
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