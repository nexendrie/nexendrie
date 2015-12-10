<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method BeerProduction|NULL getById($id)
 * @method ICollection|BeerProduction[] findByUser($user)
 * @method ICollection|BeerProduction[] findByHouse($house)
 * @method BeerProduction|NULL getLastProduction($house)
 */
class BeerProductionRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return array("Nexendrie\Orm\BeerProduction");
  }
}
?>