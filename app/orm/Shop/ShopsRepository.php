<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 * @method Shop|NULL getById($id)
 */
class ShopsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Shop::class];
  }
}
?>