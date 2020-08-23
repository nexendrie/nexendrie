<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method OrderRank|null getById(int $id)
 * @method OrderRank|null getBy(array $conds)
 * @method ICollection|OrderRank[] findBy(array $conds)
 * @method ICollection|OrderRank[] findAll()
 */
final class OrderRanksRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [OrderRank::class];
  }
}
?>