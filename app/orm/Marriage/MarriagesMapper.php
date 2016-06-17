<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class MarriagesMapper extends \Nextras\Orm\Mapper\Mapper {
  /**
   * @param int|User $user
   * @return Marriage|NULL
   */
  function getActiveMarriage($user) {
    return $this->builder()->where("status=\"active\" AND (user1=$user OR user2=$user)");
  }
}
?>