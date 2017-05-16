<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class OrdersRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Order::class];
  }
  
  /**
   * @param int $id
   * @return Order|NULL
   */
  function getById($id): ?Order {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param string $name
   * @return Order|NULL
   */
  function getByName(string $name): ?Order {
    return $this->getBy(["name" => $name]);
  }
}
?>