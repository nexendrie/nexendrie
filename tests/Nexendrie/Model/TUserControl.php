<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette\Security\User;
use Nexendrie\Orm\User as UserEntity;
use Nexendrie\Orm\Model as ORM;

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
  protected function login(string $publicname = "Trimadyl z Myhru"): void {
    /** @var User $user */
    $user = $this->getService(User::class);
    /** @var ORM $orm */
    $orm = $this->getService(ORM::class);
    $userEntity = $orm->users->getByPublicname($publicname);
    if(!is_null($userEntity)) {
      /** @var Authenticator $authenticator */
      $authenticator = $this->getService(Authenticator::class);
      $user->login($authenticator->getIdentity($userEntity));
    }
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
    /** @var Authenticator $authenticator */
    $authenticator = $user->getAuthenticator();
    $authenticator->user = $user;
    $authenticator->refreshIdentity();
    try {
      $callback();
    } finally {
      foreach($oldStats as $stat => $oldValue) {
        $data->$stat = $oldValue;
      }
      $orm->users->persistAndFlush($data);
      $authenticator->refreshIdentity();
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
    /** @var Authenticator $authenticator */
    $authenticator = $user->getAuthenticator();
    $authenticator->user = $user;
    $authenticator->refreshIdentity();
    try {
      $callback();
    } finally {
      foreach($oldStats as $stat => $oldValue) {
        $data->$stat = $oldValue;
      }
      $orm->users->persistAndFlush($data);
      $authenticator->refreshIdentity();
    }
  }
  
  /**
   * Modify user's house and perform some action with modified stats
   *
   * @throws AuthenticationNeededException
   * @throws \RuntimeException
   */
  protected function modifyHouse(array $stats, callable $callback): void {
    /** @var User $user */
    $user = $this->getService(User::class);
    if(!$user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var ORM $orm */
    $orm = $this->getService(ORM::class);
    $data = $orm->houses->getByOwner($user->id);
    if(is_null($data)) {
      throw new \RuntimeException("Current user does not own house.");
    }
    $oldStats = [];
    foreach($stats as $stat => $newValue) {
      $oldStats[$stat] = $data->$stat;
      $data->$stat = $newValue;
    }
    $orm->houses->persistAndFlush($data);
    try {
      $callback();
    } finally {
      foreach($oldStats as $stat => $oldValue) {
        $data->$stat = $oldValue;
      }
      $orm->houses->persistAndFlush($data);
    }
  }
  
  /**
   * Modify user's castle and perform some action with modified stats
   *
   * @throws AuthenticationNeededException
   * @throws \RuntimeException
   */
  protected function modifyCastle(array $stats, callable $callback): void {
    /** @var User $user */
    $user = $this->getService(User::class);
    if(!$user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var ORM $orm */
    $orm = $this->getService(ORM::class);
    $data = $orm->castles->getByOwner($user->id);
    if(is_null($data)) {
      throw new \RuntimeException("Current user does not own castle.");
    }
    $oldStats = [];
    foreach($stats as $stat => $newValue) {
      $oldStats[$stat] = $data->$stat;
      $data->$stat = $newValue;
    }
    $orm->castles->persistAndFlush($data);
    try {
      $callback();
    } finally {
      foreach($oldStats as $stat => $oldValue) {
        $data->$stat = $oldValue;
      }
      $orm->castles->persistAndFlush($data);
    }
  }
  
  /**
   * Modify user's order and perform some action with modified stats
   *
   * @throws AuthenticationNeededException
   * @throws \RuntimeException
   */
  protected function modifyOrder(array $stats, callable $callback): void {
    /** @var User $user */
    $user = $this->getService(User::class);
    if(!$user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var ORM $orm */
    $orm = $this->getService(ORM::class);
    $userEntity = $orm->users->getById($user->id);
    $data = $userEntity->order;
    if(is_null($data)) {
      throw new \RuntimeException("Current user does not belong to an order.");
    }
    $oldStats = [];
    foreach($stats as $stat => $newValue) {
      $oldStats[$stat] = $data->$stat;
      $data->$stat = $newValue;
    }
    $orm->orders->persistAndFlush($data);
    try {
      $callback();
    } finally {
      foreach($oldStats as $stat => $oldValue) {
        $data->$stat = $oldValue;
      }
      $orm->orders->persistAndFlush($data);
    }
  }
  
  public function tearDown() {
    $this->logout();
  }
}
?>