<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class EventsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Event::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Event {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @return ICollection|Event[]
   */
  public function findFromMonth(int $year = null, int $month = null): ICollection {
    $startTS = mktime(0, 0, 0, $month ?? (int) date("n"), 1, $year ?? (int) date("Y"));
    $date = new \DateTime();
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
   * @return ICollection|Event[]
   */
  public function findForTime(int $time = null): ICollection {
    return $this->findBy(["start<=" => $time, "end>=" => $time ?? time()])
      ->orderBy("start");
  }
}
?>