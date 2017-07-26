<?php
declare(strict_types = 1);

namespace Nexendrie\Model;

/**
 * TUserControl
 * Simplifies working with users in a test suit
 *
 * @author Jakub Konečný
 */
trait TUserControl {
  use \Testbench\TCompiledContainer;
  
  /**
   * Login a user
   */
  public function login(string $username = NULL, string $password = NULL): void {
    /** @var \Nette\Security\User $user */
    $user = $this->getService(\Nette\Security\User::class);
    $user->login($username ?? "admin", $password ?? "qwerty");
  }
  
  /**
   * Logout the user
   */
  public function logout(): void {
    /** @var \Nette\Security\User $user */
    $user = $this->getService(\Nette\Security\User::class);
    $user->logout(true);
  }
  
  public function tearDown() {
    $this->logout();
  }
}
?>