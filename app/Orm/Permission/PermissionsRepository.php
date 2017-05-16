<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class PermissionsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Permission::class];
  }
  
  /**
   * @param int $id
   * @return Permission|NULL
   */
  function getById($id): ?Permission {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param Group|int $group
   * @return ICollection|Permission[]
   */
  function findByGroup($group): ICollection {
    return $this->findBy(["group" => $group]);
  }
}
?>