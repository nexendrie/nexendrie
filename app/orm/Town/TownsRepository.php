<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Repository\Repository,
    Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Town|NULL getById($id)
 * @method ICollection|Town[] findByOwner($owner)
 */
class TownsRepository extends Repository {
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