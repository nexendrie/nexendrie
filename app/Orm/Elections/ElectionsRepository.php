<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class ElectionsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Election::class];
  }
  
  /**
   * @param int $id
   * @return Election|NULL
   */
  function getById($id): ?Election {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param Town|int $town
   * @return ICollection|Election[]
   */
  function findByTown($town): ICollection {
    return $this->findBy(["town" => $town]);
  }
  
  /**
   * Get votes from specified town and month
   * 
   * @param Town|int $town
   * @param int $year
   * @param int $month
   * @return ICollection|Election[]
   */
  function findVotedInMonth($town, int $year, int $month): ICollection {
    $startOfMonthTS = mktime(0, 0, 0, $month, 1, $year);
    $date = new \DateTime;
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp();
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp();
    return $this->findBy(["town" => $town, "when>" => $start, "when<" => $end]);
  }
}
?>