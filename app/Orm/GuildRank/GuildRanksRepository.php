<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class GuildRanksRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames(): array {
    return [GuildRank::class];
  }
  
  /**
   * @param int $id
   * @return GuildRank|NULL
   */
  function getById($id): ?GuildRank {
    return $this->getBy(["id" => $id]);
  }
}
?>