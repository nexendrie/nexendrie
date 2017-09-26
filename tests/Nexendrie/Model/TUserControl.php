<?php
declare(strict_types = 1);

namespace Nexendrie\Model;

use Nette\Security\User,
    Nexendrie\Orm\User as UserEntity,
    Nexendrie\Orm\Model as ORM;

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
    /** @var User $user */
    $user = $this->getService(User::class);
    $user->login($username, $password);
  }
  
  /**
   * Logout the user
   */
  protected function logout(): void {
    /** @var User $user */
    $user = $this->getService(User::class);
    $user->logout(true);
  }
  
  /**
   * @throws AuthenticationNeededException
   */
  protected function getUser(): UserEntity {
    /** @var User $user */
    $user = $this->getService(User::class);
    if(!$user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var ORM $orm */
    $orm = $this->getService(ORM::class);
    return $orm->users->getById($user->id);
  }
  
  /**
   * @return mixed
   * @throws AuthenticationNeededException
   */
  protected function getUserStat(string $stat) {
    /** @var User $user */
    $user = $this->getService(User::class);
    if(!$user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var ORM $orm */
    $orm = $this->getService(ORM::class);
    $data = $orm->users->getById($user->id);
    return $data->$stat;
  }
  
  /**
   * Perform an action and revert some stats to original values
   *
   * @param string[] $stats
   * @throws AuthenticationNeededException
   */
  protected function preserveStats(array $stats, callable $callback): void {
    /** @var User $user */
    $user = $this->getService(User::class);
    if(!$user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var ORM $orm */
    $orm = $this->getService(ORM::class);
    $data = $orm->users->getById($user->id);
    $oldStats = [];
    foreach($stats as $stat) {
      $oldStats[$stat] = $data->$stat;
    }
    $orm->users->persistAndFlush($data);
    /** @var UserManager $userManager */
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
  
  /**
   * Modify the user and perform some action with modified stats
   *
   * @throws AuthenticationNeededException
   */
  protected function modifyUser(array $stats, callable $callback): void {
    /** @var User $user */
    $user = $this->getService(User::class);
    if(!$user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var ORM $orm */
    $orm = $this->getService(ORM::class);
    $data = $orm->users->getById($user->id);
    $oldStats = [];
    foreach($stats as $stat => $newValue) {
      $oldStats[$stat] = $data->$stat;
      $data->$stat = $newValue;
    }
    $orm->users->persistAndFlush($data);
    /** @var UserManager $userManager */
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