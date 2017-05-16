<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Dbal\QueryBuilder\QueryBuilder;

/**
 * @author Jakub Konečný
 */
class MarriagesMapper extends \Nextras\Orm\Mapper\Mapper {
  /**
   * @param int|User $user
   * @return QueryBuilder
   */
  function getActiveMarriage($user): QueryBuilder {
    return $this->builder()->where("status=\"active\" AND (user1=$user OR user2=$user)");
  }
  
  /**
   * @param int|User $user
   * @return QueryBuilder
   */
  function getAcceptedMarriage($user): QueryBuilder {
    return $this->builder()->where("status=\"accepted\" AND (user1=$user OR user2=$user)");
  }
}
?>