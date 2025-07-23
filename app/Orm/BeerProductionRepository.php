<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method BeerProduction|null getById(int $id)
 * @method BeerProduction|null getBy(array $conds)
 * @method ICollection|BeerProduction[] findBy(array $conds)
 * @method ICollection|BeerProduction[] findAll()
 */
final class BeerProductionRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [BeerProduction::class];
  }
  
  /**
   * @return ICollection|BeerProduction[]
   */
  public function findByUser(User|int $user): ICollection {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * @return ICollection|BeerProduction[]
   */
  public function findByHouse(House|int $house): ICollection {
    return $this->findBy(["house" => $house]);
  }

  public function getLastProduction(House|int $house): ?BeerProduction {
    return $this->findBy(["house" => $house]) // @phpstan-ignore return.type
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
    $startOfMonthTS = (int) mktime(0, 0, 0, $month, 1, $year);
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