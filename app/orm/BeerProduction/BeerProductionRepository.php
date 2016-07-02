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
    return [BeerProduction::class];
  }
  
  /**
   * Get beer made this month by specified user
   * 
   * @param int $user
   * @return ICollection|MonasteryDonation[]
   */
  function findProducedThisMonth($user) {
    $month = date("n");
    $year = date("Y");
    $startOfMonthTS = mktime(0, 0, 0, $month, 1, $year);
    $date = new \DateTime;
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp();
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp();
    return $this->findBy(array("user" => $user, "when>" => $start, "when<" => $end));
  }
}
?>