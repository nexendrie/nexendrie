<?php
declare(strict_types=1);

namespace Nexendrie\Menu;

use Nette\Security\User;

/**
 * ConditionPath
 *
 * @author Jakub Konečný
 */
final class ConditionPath extends BaseCondition {
  /** @var User */
  protected $user;
  
  public function __construct(User $user) {
    $this->user = $user;
  }
  
  /**
   * @param string $parameter
   * @throws \InvalidArgumentException
   */
  public function isAllowed($parameter = null): bool {
    if(!$this->user->isLoggedIn()) {
      return false;
    } elseif(!is_string($parameter)) {
      throw new \InvalidArgumentException("Method " . static::class . "::isAllowed expects string as parameter.");
    }
    return ($parameter === $this->user->identity->path);
  }
}
?>