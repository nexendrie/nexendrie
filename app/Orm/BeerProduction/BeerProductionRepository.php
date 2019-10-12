<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class BeerProductionRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [BeerProduction::class];
  }
  
  /**
   * @param int $id
   * @return BeerProduction|null
   */
  public function getById($id): ?\Nextras\Orm\Entity\IEntity {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $user
   * @return ICollection|BeerProduction[]
   */
  public function findByUser($user): ICollection {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * @param House|int $house
   * @return ICollection|BeerProduction[]
   */
  public function findByHouse($house): ICollection {
    return $this->findBy(["house" => $house]);
  }
  
  /**
   * @param House|int $house
   */
  public function getLastProduction($house): ?BeerProduction {
    return $this->findBy(["house" => $house])
      ->orderBy("created", ICollection::DESC)
      ->limitBy(1)
      ->fetch();
  }
  
  /**
   * Get beer made this month by specified user
   *
   * @return ICollection|BeerProduction[]
   */
  public function findProducedThisMonth(int $user): ICollection {
    $month = (int) date("n");
    $year = (int) date("Y");
    $startOfMonthTS = mktime(0, 0, 0, $month, 1, $year);
    $date = new \DateTime();
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp();
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp();
    return $this->findBy(["user" => $user, "created>" => $start, "created<" => $end]);
  }
}
?>