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
   *
   * @param string $username
   * @param string $password
   * @return void
   */
  function login($username = "", $password = "") {
    /** @var \Nette\Security\User $user */
    $user = $this->getService(\Nette\Security\User::class);
    if($username === "") $username = getenv("APP_USER");
    if($password === "") $password = getenv("APP_PASSWORD");
    $user->login($username, $password);
  }
  
  /**
   * Logout the user
   *
   * @return void
   */
  function logout() {
    /** @var \Nette\Security\User $user */
    $user = $this->getService(\Nette\Security\User::class);
    $user->logout(true);
  }
  
  function tearDown() {
    $this->logout();
  }
}
?>