<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Mapper\Mapper;

/**
 * @author Jakub Konečný
 */
class UserAdventuresMapper extends Mapper {
  /**
   * Get user's last adventure
   * 
   * @param int $user
   * @return UserAdventure|NULL
   */
  function getLastAdventure($user) {
    return $this->builder()->where("user=$user")->orderBy("started DESC")->limitBy(1);
  }
}
?>