<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method BeerProduction|NULL getLastProduction($house)
 */
class BeerProductionRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [BeerProduction::class];
  }
  
  /**
   * @param int $id
   * @return BeerProduction|NULL
   */
  function getById($id) {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $user
   * @return ICollection|BeerProduction[]
   */
  function findByUser($user) {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * @param House|int $house
   * @return ICollection|BeerProduction[]
   */
  function findByHouse($house) {
    return $this->findBy(["house" => $house]);
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
    $startOfMonthTS = mktime(0, 0, 0, (int) $month, 1, (int) $year);
    $date = new \DateTime;
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp();
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp();
    return $this->findBy(["user" => $user, "when>" => $start, "when<" => $end]);
  }
}
?>