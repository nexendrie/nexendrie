<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
final class OrderRanksRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [OrderRank::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?OrderRank {
    return $this->getBy(["id" => $id]);
  }
}
?>