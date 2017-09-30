<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class ShopsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Shop::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Shop {
    return $this->getBy(["id" => $id]);
  }
}
?>