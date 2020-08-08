<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class PermissionsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Permission::class];
  }
  
  /**
   * @param int $id
   * @return Permission|null
   */
  public function getById($id): ?\Nextras\Orm\Entity\IEntity {
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