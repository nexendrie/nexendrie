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
  protected function login(string $username = "admin", string $password = "qwerty"): void {
    /** @var \Nette\Security\User $user */
    $user = $this->getService(\Nette\Security\User::class);
    $user->login($username, $password);
  }
  
  /**
   * Logout the user
   */
  protected function logout(): void {
    /** @var \Nette\Security\User $user */
    $user = $this->getService(\Nette\Security\User::class);
    $user->logout(true);
  }
  
  /**
   * @throws AuthenticationNeededException
   */
  protected function getUser(): \Nexendrie\Orm\User {
    /** @var \Nette\Security\User $user */
    $user = $this->getService(\Nette\Security\User::class);
    if(!$user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    /** @var \Nexendrie\Orm\Model $orm */
    $orm = $this->getService(\Nexendrie\Orm\Model::class);
    return $orm->users->getById($user->id);
  }
  
  /**
   * @return mixed
   * @throws AuthenticationNeededException
   */
  protected function getUserStat(string $stat) {
    /** @var \Nette\Security\User $user */
    $user = $this->getService(\Nette\Security\User::class);
    if(!$user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    /** @var \Nexendrie\Orm\Model $orm */
    $orm = $this->getService(\Nexendrie\Orm\Model::class);
    $data = $orm->users->getById($user->id);
    return $data->$stat;
  }
  
  /**
   * Modify the user and perform some action with modified stats
   *
   * @throws AuthenticationNeededException
   */
  protected function modifyUser(array $stats, callable $callback): void {
    /** @var \Nette\Security\User $user */
    $user = $this->getService(\Nette\Security\User::class);
    if(!$user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    /** @var \Nexendrie\Orm\Model $orm */
    $orm = $this->getService(\Nexendrie\Orm\Model::class);
    $data = $orm->users->getById($user->id);
    $oldStats = [];
    foreach($stats as $stat => $newValue) {
      $oldStats[$stat] = $data->$stat;
      $data->$stat = $newValue;
    }
    $orm->users->persistAndFlush($data);
    /** @var \Nexendrie\Model\UserManager $userManager */
    $userManager = $user->getAuthenticator();
    $userManager->user = $user;
    $userManager->refreshIdentity();
    try {
      $callback();
    } finally {
      foreach($oldStats as $stat => $oldValue) {
        $data->$stat = $oldValue;
      }
      $orm->users->persistAndFlush($data);
      $userManager->refreshIdentity();
    }
  }
  
  public function tearDown() {
    $this->logout();
  }
}
?>