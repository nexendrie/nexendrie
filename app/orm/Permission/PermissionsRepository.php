<?php
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
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * @param Group|int $group
   * @return ICollection|Permission[]
   */
  function findByGroup($group) {
    return $this->findBy(array("group" => $group));
  }
}
?>