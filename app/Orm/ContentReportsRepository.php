<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * ContentReportsRepository
 *
 * @author Jakub Konečný
 * @method ContentReport|null getById(int $id)
 * @method ContentReport|null getBy(array $conds)
 * @method ICollection|ContentReport[] findBy(array $conds)
 * @method ICollection|ContentReport[] findAll()
 */
final class ContentReportsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [ContentReport::class];
  }
}
?>