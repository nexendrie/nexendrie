<?php
declare(strict_types=1);

namespace Nexendrie\Menu;

use Nette\Security\User;

/**
 * BannedCondition
 *
 * @author Jakub Konečný
 */
final class ConditionBanned extends BaseCondition {
  protected User $user;
  
  public function __construct(User $user) {
    $this->user = $user;
  }
  
  /**
   * @param bool $parameter
   * @throws \InvalidArgumentException
   */
  public function isAllowed($parameter = null): bool {
    if(!$this->user->isLoggedIn()) {
      return false;
    } elseif(!is_bool($parameter)) {
      throw new \InvalidArgumentException("Method " . static::class . "::isAllowed expects boolean as parameter.");
    }
    return ($parameter === $this->user->identity->banned);
  }
}
?>