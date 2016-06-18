<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Marriage as MarriageEntity;

/**
 * Marriage Model
 *
 * @author Jakub Konečný
 */
class Marriage extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all marriages
   * 
   * @return MarriageEntity[]
   */
  function listOfMarriages() {
    return $this->orm->marriages->findAll();
  }
  
  /**
   * Check whetever the user can propose someone
   *  
   * @param int $id
   * @return bool
   */
  function canPropose($id) {
    if(!$this->user->isLoggedIn()) return false;
    elseif($id === $this->user->id) return false;
    $user = $this->orm->users->getById($id);
    if(!$user) return false;
    elseif(!is_null($this->orm->marriages->getActiveMarriage($id)->fetch())) return false;
    $me = $this->orm->users->getById($this->user->id);
    if(!is_null($this->orm->marriages->getActiveMarriage($this->user->id)->fetch())) return false;
    elseif($user->group->path != $me->group->path) return false;
    return true;
  }
  
  /**
   * Propose someone marriage
   * 
   * @param int $id
   * @return void
   * @throws CannotProposeMarriageException
   */
  function proposeMarriage($id) {
    if(!$this->canPropose($id)) throw new CannotProposeMarriageException;
    $marriage = new MarriageEntity;
    $this->orm->marriages->attach($marriage);
    $marriage->user1 = $this->user->id;
    $marriage->user2 = $id;
    $marriage->term = time() + (60 * 60 * 24 * 14);
    $this->orm->marriages->persistAndFlush($marriage);
  }
}

class CannotProposeMarriageException extends AccessDeniedException {
  
}
?>