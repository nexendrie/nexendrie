<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Order|null getById(int $id)
 * @method Order|null getBy(array $conds)
 * @method ICollection|Order[] findBy(array $conds)
 * @method ICollection|Order[] findAll()
 */
final class OrdersRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Order::class];
  }
  
  public function getByName(string $name): ?Order {
    return $this->getBy(["name" => $name]);
  }
}
?>