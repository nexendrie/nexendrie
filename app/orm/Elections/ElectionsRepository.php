<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Election|NULL getById($id)
 * @method ICollection|Election[] findByTown($town)
 */
class ElectionsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Election::class];
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
    $startOfMonthTS = mktime(0, 0, 0, $month, 1, $year);
    $date = new \DateTime;
    $date->setTimestamp($startOfMonthTS);
    $start = $date->getTimestamp();
    $date->modify("+ 1 month");
    $date->modify("- 1 second");
    $end = $date->getTimestamp();
    return $this->findBy(array("town" => $town, "when>" => $start, "when<" => $end));
  }
}
?>