<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class GuildRanksRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [GuildRank::class];
  }
  
  /**
   * @param int $id
   * @return GuildRank|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
}
?>