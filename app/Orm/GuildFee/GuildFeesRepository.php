<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * GuildFeesRepository
 *
 * @author Jakub Konečný
 */
final class GuildFeesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [GuildFee::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?GuildFee {
    return $this->getBy(["id" => $id]);
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