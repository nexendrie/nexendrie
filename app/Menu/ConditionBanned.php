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
  public function __construct(private readonly User $user) {
  }
  
  /**
   * @param bool $parameter
   * @throws \InvalidArgumentException
   */
  public function isAllowed($parameter = null): bool {
    if(!$this->user->isLoggedIn()) {
      return false;
    } elseif(!is_bool($parameter)) {
      throw new \InvalidArgumentException("Method " . self::class . "::isAllowed expects boolean as parameter.");
    }
    return ($parameter === $this->user->identity->banned);
  }
}
?>