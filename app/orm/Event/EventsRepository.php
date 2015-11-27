<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * 
 * @method Event|NULL getById($id)
 */
class EventsRepository extends \Nextras\Orm\Repository\Repository {
  /**
   * Get events from specified month
   * 
   * @param int $year
   * @param int $month
   * @return ICollection|Event[]
   */
  function findFromMonth($year = 0, $month = 0) {
    if($month === 0) $month = date("n");
    if($year === 0) $year = date("Y");
    $startTS = mktime(0, 0, 0, $month, 1, $year);
    $date = new \DateTime;
    $date->setTimestamp($startTS);
    $date->modify("+1 month");
    $date->modify("-1 second");
    $endTS = $date->getTimestamp();
    return $this->findBy(array("start<=" => $endTS, "end>=" => $startTS))
      ->orderBy("start");
  }
}
?>