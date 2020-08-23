<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method GuildRank|null getById(int $id)
 * @method GuildRank|null getBy(array $conds)
 * @method ICollection|GuildRank[] findBy(array $conds)
 * @method ICollection|GuildRank[] findAll()
 */
final class GuildRanksRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [GuildRank::class];
  }
}
?>