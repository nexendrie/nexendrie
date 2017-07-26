<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class PermissionsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames(): array {
    return [Permission::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Permission {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param Group|int $group
   * @return ICollection|Permission[]
   */
  public function findByGroup($group): ICollection {
    return $this->findBy(["group" => $group]);
  }
}
?>