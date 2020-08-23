<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Shop|null getById(int $id)
 * @method Shop|null getBy(array $conds)
 * @method ICollection|Shop[] findBy(array $conds)
 * @method ICollection|Shop[] findAll()
 */
final class ShopsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Shop::class];
  }
}
?>