<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class GroupsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Group::class];
  }
  
  /**
   * @param int $id
   * @return Group|NULL
   */
  function getById($id): ?Group {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param int $level
   * @return Group|NULL
   */
  function getByLevel(int $level): ?Group {
    return $this->getBy(["level" => $level]);
  }
}
?>