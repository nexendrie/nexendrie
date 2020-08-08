<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
final class MealsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Meal::class];
  }
  
  /**
   * @param int $id
   * @return Meal|null
   */
  public function getById($id): ?\Nextras\Orm\Entity\IEntity {
    return $this->getBy(["id" => $id]);
  }
}
?>