<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * GuildFeesRepository
 *
 * @author Jakub Konečný
 * @method GuildFee|null getById(int $id)
 * @method GuildFee|null getBy(array $conds)
 * @method ICollection|GuildFee[] findBy(array $conds)
 * @method ICollection|GuildFee[] findAll()
 */
final class GuildFeesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [GuildFee::class];
  }
  
  /**
   * @param User|int $user
   * @param Guild|int $guild
   */
  public function getByUserAndGuild($user, $guild): ?GuildFee {
    return $this->getBy([
      "user" => $user, "guild" => $guild,
    ]);
  }
}
?>