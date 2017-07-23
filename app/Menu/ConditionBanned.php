<?php
declare(strict_types=1);

namespace Nexendrie\Menu;

use Nette\Security\User;

/**
 * BannedCondition
 *
 * @author Jakub Konečný
 */
class ConditionBanned extends BaseCondition {
  /** @var User */
  protected $user;
  
  public function __construct(User $user) {
    $this->user = $user;
  }
  
  /**
   * @param bool $parameter
   * @throws \InvalidArgumentException
   */
  public function isAllowed($parameter = NULL): bool {
    if(!$this->user->isLoggedIn()) {
      return false;
    } elseif(!is_bool($parameter)) {
      throw new \InvalidArgumentException("Method " . static::class ."::isAllowed expects boolean as parameter.");
    }
    return ($parameter === $this->user->identity->banned);
  }
}
?>