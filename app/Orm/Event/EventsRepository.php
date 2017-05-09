<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class EventsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Event::class];
  }
  
  /**
   * @param int $id
   * @return Event|NULL
   */
  function getById($id) {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * Get events from specified month
   * 
   * @param int $year
   * @param int $month
   * @return ICollection|Event[]
   */
  function findFromMonth(int $year = 0, int $month = 0): ICollection {
    if($month === 0) {
      $month = (int) date("n");
    }
    if($year === 0) {
      $year = (int) date("Y");
    }
    $startTS = mktime(0, 0, 0, $month, 1, $year);
    $date = new \DateTime;
    $date->setTimestamp($startTS);
    $date->modify("+1 month");
    $date->modify("-1 second");
    $endTS = $date->getTimestamp();
    return $this->findBy(["start<=" => $endTS, "end>=" => $startTS])
      ->orderBy("start");
  }
  /**
   * Get ongoing events (at specified time)
   * 
   * @param int $time
   * @return ICollection|Event[]
   */
  function findForTime(int $time = NULL): ICollection {
    return $this->findBy(["start<=" => $time, "end>=" => $time ?? time()])
      ->orderBy("start");
  }
}
?>