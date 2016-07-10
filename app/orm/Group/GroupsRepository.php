<?php
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
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * @param int $level
   * @return Group|NULL
   */
  function getByLevel($level) {
    return $this->getBy(array("level" => $level));
  }
}
?>