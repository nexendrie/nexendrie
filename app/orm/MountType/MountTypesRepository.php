<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 * @method MountType|NULL getById($id)
 */
class MountTypesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [MountType::class];
  }
}
?>