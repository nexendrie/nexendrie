<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class GroupsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Group::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Group {
    return $this->getBy(["id" => $id]);
  }
  
  public function getByLevel(int $level): ?Group {
    return $this->getBy(["level" => $level]);
  }
}
?>