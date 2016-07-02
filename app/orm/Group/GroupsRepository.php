<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 * @method Group|NULL getById($id)
 * @method Group|NULL getByLevel($level)
 */
class GroupsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Group::class];
  }
}
?>