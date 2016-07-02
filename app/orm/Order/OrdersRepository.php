<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 * @method Order|NULL getById($id)
 * @method Order|NULL getByName($name)
 */
class OrdersRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Order::class];
  }
}
?>