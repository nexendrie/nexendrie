<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class MountTypesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [MountType::class];
  }
  
  /**
   * @param int $id
   * @return MountType|NULL
   */
  function getById($id): ?MountType {
    return $this->getBy(["id" => $id]);
  }
}
?>