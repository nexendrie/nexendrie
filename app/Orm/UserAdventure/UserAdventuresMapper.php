<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class UserAdventuresMapper extends \Nextras\Orm\Mapper\Mapper {
  /**
   * Get user's last adventure
   * 
   * @param int $user
   * @return UserAdventure|NULL
   */
  function getLastAdventure(int $user) {
    return $this->builder()->where("user=$user")->orderBy("started DESC")->limitBy(1);
  }
}
?>