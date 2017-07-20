<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class MarriagesMapper extends \Nextras\Orm\Mapper\Mapper {
  /**
   * @param int|User $user
   * @return Marriage|NULL
   */
  public function getActiveMarriage($user): ?Marriage {
    return $this->toCollection(
        $this->builder()
          ->where("status='active' AND (user1=$user OR user2=$user)")
          ->limitBy(1)
    )
      ->fetch();
  }
  
  /**
   * @param int|User $user
   * @return Marriage|NULL
   */
  public function getAcceptedMarriage($user): ?Marriage {
    return $this->toCollection(
        $this->builder()
          ->where("status='accepted' AND (user1=$user OR user2=$user)")
          ->limitBy(1)
    )
      ->fetch();
  }
}
?>