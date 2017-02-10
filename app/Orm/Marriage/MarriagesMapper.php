<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class MarriagesMapper extends \Nextras\Orm\Mapper\Mapper {
  /**
   * @param int|User $user
   * @return \Nextras\Dbal\QueryBuilder\QueryBuilder
   */
  function getActiveMarriage($user) {
    return $this->builder()->where("status=\"active\" AND (user1=$user OR user2=$user)");
  }
  
  /**
   * @param int|User $user
   * @return \Nextras\Dbal\QueryBuilder\QueryBuilder
   */
  function getAcceptedMarriage($user) {
    return $this->builder()->where("status=\"accepted\" AND (user1=$user OR user2=$user)");
  }
}
?>