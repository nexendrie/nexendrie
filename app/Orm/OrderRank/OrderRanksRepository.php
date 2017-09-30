<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class OrderRanksRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [OrderRank::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id) {
    return $this->getBy(["id" => $id]);
  }
}
?>