<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
final class ShopsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Shop::class];
  }
  
  /**
   * @param int $id
   * @return Shop|null
   */
  public function getById($id): ?\Nextras\Orm\Entity\IEntity {
    return $this->getBy(["id" => $id]);
  }
}
?>