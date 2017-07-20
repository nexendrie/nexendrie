<?php
declare(strict_types=1);

namespace Nexendrie\Menu;

use Nette\Security\User;

/**
 * BannedCondition
 *
 * @author Jakub Konečný
 */
class ConditionBanned implements IMenuItemCondition {
  use \Nette\SmartObject;
  
  /** @var User */
  protected $user;
  /** @var string */
  protected $name = "banned";
  
  function __construct(User $user) {
    $this->user = $user;
  }
  
  function getName(): string {
    return $this->name;
  }
  
  /**
   * @param bool $parameter
   * @return bool
   * @throws \InvalidArgumentException
   */
  function isAllowed($parameter = NULL): bool {
    if(!$this->user->isLoggedIn()) {
      return false;
    } elseif(!is_bool($parameter)) {
      throw new \InvalidArgumentException("Method " . static::class ."::isAllowed expects boolean as parameter.");
    }
    return ($parameter === $this->user->identity->banned);
  }
}
?>