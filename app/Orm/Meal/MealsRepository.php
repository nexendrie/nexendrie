<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class MealsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Meal::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Meal {
    return $this->getBy(["id" => $id]);
  }
}
?>