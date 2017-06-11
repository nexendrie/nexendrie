<?php
declare(strict_types=1);

namespace Nexendrie\Menu;

use Nette\Security\User;

/**
 * ConditionPath
 *
 * @author Jakub Konečný
 */
class ConditionPath implements IMenuItemCondition {
  use \Nette\SmartObject;
  
  /** @var User */
  protected $user;
  /** @var string */
  protected $name = "path";
  
  function __construct(User $user) {
    $this->user = $user;
  }
  
  /**
   * @return string
   */
  function getName(): string {
    return $this->name;
  }
  
  /**
   * @param string $parameter
   * @return bool
   * @throws \InvalidArgumentException
   */
  function isAllowed($parameter = NULL): bool {
    if(!$this->user->isLoggedIn()) {
      return false;
    } elseif(!is_string($parameter)) {
      throw new \InvalidArgumentException("Method " . static::class . "::isAllowed expects string as parameter.");
    }
    return ($parameter === $this->user->identity->path);
  }
}
?>