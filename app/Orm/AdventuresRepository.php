<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class AdventuresRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Adventure::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Adventure {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param Event|int $event
   * @return ICollection|Adventure[]
   */
  public function findByEvent($event): ICollection {
    return $this->findBy(["event" => $event]);
  }
  
  /**
   * Find adventures for specified level
   *
   * @return ICollection|Adventure[]
   */
  public function findForLevel(int $level): ICollection {
    return $this->findBy(["level<=" => $level]);
  }
}
?>