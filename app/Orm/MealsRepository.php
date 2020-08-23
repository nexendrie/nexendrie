<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Meal|null getById(int $id)
 * @method Meal|null getBy(array $conds)
 * @method ICollection|Meal[] findBy(array $conds)
 * @method ICollection|Meal[] findAll()
 */
final class MealsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Meal::class];
  }
}
?>