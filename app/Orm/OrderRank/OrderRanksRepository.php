<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class OrderRanksRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [OrderRank::class];
  }
  
  /**
   * @param int $id
   * @return OrderRank|NULL
   */
  function getById($id) {
    return $this->getBy(["id" => $id]);
  }
}
?>