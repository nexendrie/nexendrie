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
   * @return Adventure|null
   */
  public function getById($id): ?\Nextras\Orm\Entity\IEntity {
    return $this->getBy(["id" => $id]);
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