<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 * @method Meal|NULL getById($id)
 */
class MealsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Meal::class];
  }
}
?>