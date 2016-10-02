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
  function getById($id) {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param Town|int $town
   * @return Election|NULL
   */
  function findByTown($town) {
    return $this->findBy(["town" => $town]);
  }
  
  /**
   * Get votes from specified town and month
   * 
   * @param int $town
   * @param int $year
   * @param int $month
   * @return ICollection|Election[]
   */
  function findVotedInMonth($town, $year, $month) {
    $startOfMonthTS = mktime(0, 0, 0, (int) $month, 1, (int) $year);
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