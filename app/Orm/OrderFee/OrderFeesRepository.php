<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * OrderFeesRepository
 *
 * @author Jakub Konečný
 */
class OrderFeesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [OrderFee::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?OrderFee {
    return $this->getBy(["id" => $id]);
  }
  
  public function getByUserAndOrder($user, $order): ?OrderFee {
    return $this->getBy([
      "user" => $user, "order" => $order,
    ]);
  }
}
?>