<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Adventure|null getById(int $id)
 * @method Adventure|null getBy(array $conds)
 * @method ICollection|Adventure[] findBy(array $conds)
 * @method ICollection|Adventure[] findAll()
 */
final class AdventuresRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Adventure::class];
  }
  
  /**
   * @return ICollection|Adventure[]
   */
  public function findByEvent(Event|int $event): ICollection {
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