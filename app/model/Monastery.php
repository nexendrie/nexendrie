<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Monastery as MonasteryEntity;

/**
 * Monastery Model
 *
 * @author Jakub Konečný
 */
class Monastery extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all monasteries
   * 
   * @return MonasteryEntity[]
   */
  function listOfMonasteries() {
    return $this->orm->monasterires->findAll();
  }
  
  /**
   * Get specified monastery
   * 
   * @param int $id
   * @return MonasteryEntity
   * @throws MonasteryNotFoundException
   */
  function get($id) {
    $monastery = $this->orm->monasterires->getById($id);
    if(!$monastery) throw new MonasteryNotFoundException;
    else return $monastery;
  }
  
  /**
   * Get specified user's monastary
   * 
   * @param int $id
   * @return MonasteryEntity
   * @throws UserNotFoundException
   * @throws NotInMonasteryException
   */
  function getByUser($id = 0) {
    if($id === 0) $id = $this->user->id;
    $user = $this->orm->users->getById($id);
    if(!$user) throw new UserNotFoundException;
    elseif(!$user->monastery) throw new NotInMonasteryException;
    else return $user->monastery;
  }
  
  /**
   * Check whetever the user can join a monastery
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canJoin() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $canJoin = false;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery AND $user->group->path === "city") $canJoin = true;
    return $canJoin;
  }
  
  /**
   * Join a monastery
   * 
   * @param int $id
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotJoinMonasteryException
   * @throws MonasteryNotFoundException
   */
  function join($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canJoin()) throw new CannotJoinMonasteryException;
    try {
      $monastery = $this->get($id);
    } catch(MonasteryNotFoundException $e) {
      throw $e;
    }
    $user = $this->orm->users->getById($this->user->id);
    $user->monastery = $monastery;
    $user->group = $this->orm->groups->getByLevel(55);
    $user->town = $monastery->town;
    $this->orm->users->persistAndFlush($user);
    $this->user->identity->group = $user->group->id;
    $this->user->identity->level = $user->group->level;
    $this->user->identity->town = $user->town->id;
  }
  
  /**
   * Check whetever the user can pray (now)
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canPray() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery) return false;
    elseif($user->life >= $user->maxLife) return false;
    elseif(!$user->lastPrayer) return true;
    $oneDay = 60 * 60 * 24;
    if($user->lastPrayer + $oneDay > time()) return true;
    else return false;
  }
  
  /**
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotPrayException
   */
  function pray() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    if(!$this->canPray()) throw new CannotPrayException;
    $user = $this->orm->users->getById($this->user->id);
    $user->lastPrayer = time();
    $user->life += 5;
    $user->prayers++;
    $this->orm->users->persistAndFlush($user);
  }
}

class MonasteryNotFoundException extends RecordNotFoundException {
  
}

class NotInMonasteryException extends AccessDeniedException {
  
}

class CannotJoinMonasteryException extends AccessDeniedException {
  
}

class CannotPrayException extends AccessDeniedException {
  
}
?>