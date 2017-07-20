<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class MealsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames(): array {
    return [Meal::class];
  }
  
  /**
   * @param int $id
   * @return Meal|NULL
   */
  public function getById($id): ?Meal {
    return $this->getBy(["id" => $id]);
  }
}
?>