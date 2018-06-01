<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
final class OrdersRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Order::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Order {
    return $this->getBy(["id" => $id]);
  }
  
  public function getByName(string $name): ?Order {
    return $this->getBy(["name" => $name]);
  }
}
?>