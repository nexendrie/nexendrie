<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Town|NULL getById($id)
 * @method ICollection|Town[] findByOwner($owner)
 * @method Town|NULL getByName($name)
 */
class TownsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Town::class];
  }
  
  /**
   * Get towns on sale
   * 
   * @return ICollection|Town[]
   */
  function findOnMarket() {
    return $this->findBy(array("onMarket" => true));
  }
}
?>