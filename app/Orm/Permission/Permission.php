<?php
namespace Nexendrie\Orm;

/**
 * Permission
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $resource
 * @property string $action
 * @property Group $group {m:1 Group::$permissions}
 */
class Permission extends \Nextras\Orm\Entity\Entity {
  /**
   * @return \Nexendrie\Orm\PermissionDummy
   */
  function dummy() {
    return new PermissionDummy($this);
  }
}
?>