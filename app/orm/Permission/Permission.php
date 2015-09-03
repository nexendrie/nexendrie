<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * Permission
 *
 * @author Jakub Konečný
 * @property string $resource
 * @property string $action
 * @property Group $group {m:1 Group::$permissions}
 */
class Permission extends Entity {
  
}
?>