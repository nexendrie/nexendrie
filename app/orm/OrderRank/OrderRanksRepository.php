<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 * @method OrderRank|NULL getById($id)
 */
class OrderRanksRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [OrderRank::class];
  }
}
?>