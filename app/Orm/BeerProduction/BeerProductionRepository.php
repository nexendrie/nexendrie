<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
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
  function findByUser($user): ICollection {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * @param House|int $house
   * @return ICollection|BeerProduction[]
   */
  function findByHouse($house): ICollection {
    return $this->findBy(["house" => $house]);
  }
  
  /**
   * @param House|int $house
   * @return BeerProduction|null
   */
  function getLastProduction($house): ?BeerProduction {
    return $this->findBy(["house" => $house])
      ->orderBy("when", ICollection::DESC)
      ->fetch();
  }
  
  /**
   * Get beer made this month by specified user
   * 
   * @param int $user
   * @return ICollection|BeerProduction[]
   */
  function findProducedThisMonth(int $user): ICollection {
    $month = (int) date("n");
    $year = (int) date("Y");
    $startOfMonthTS = mktime(0, 0, 0, $month, 1, $year);
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