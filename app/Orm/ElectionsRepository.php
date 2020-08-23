<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Election|null getById(int $id)
 * @method Election|null getBy(array $conds)
 * @method ICollection|Election[] findBy(array $conds)
 * @method ICollection|Election[] findAll()
 */
final class ElectionsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Election::class];
  }
  
  /**
   * @param Town|int $town
   * @return ICollection|Election[]
   */
  public function findByTown($town): ICollection {
    return $this->findBy(["town" => $town]);
  }
  
  /**
   * @param Town|int $town
   * @return ICollection|Election[]
   */
  public function findVotedInMonth($town, int $year, int $month): ICollection {
    $startOfMonthTS = mktime(0, 0, 0, $month, 1, $year);
    $date = new \DateTime();
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp();
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp();
    return $this->findBy(["town" => $town, "created>" => $start, "created<" => $end]);
  }
}
?>