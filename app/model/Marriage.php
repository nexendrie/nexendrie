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
    elseif(!is_null($this->orm->marriages->getAcceptedMarriage($id)->fetch())) return false;
    $me = $this->orm->users->getById($this->user->id);
    if(!is_null($this->orm->marriages->getActiveMarriage($this->user->id)->fetch())) return false;
    elseif(!is_null($this->orm->marriages->getAcceptedMarriage($this->user->id)->fetch())) return false;
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
  
  /**
   * Get proposals for a user
   * 
   * @return MarriageEntity[]
   * @throws AuthenticationNeededException
   */
  function listOfProposals() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    else return $this->orm->marriages->findProposals($this->user->id);
  }
  
  /**
   * Accept a marriage proposal
   * 
   * @param int $id
   * @return void
   * @throws AuthenticationNeededException
   * @throws MarriageNotFoundException
   * @throws AccessDeniedException
   * @throws CannotProposeMarriageException
   * @throws MarriageProposalAlreadyHandledException
   */
  function acceptProposal($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $proposal = $this->orm->marriages->getById($id);
    if(!$proposal) throw new MarriageNotFoundException;
    elseif($proposal->user2->id != $this->user->id) throw new AccessDeniedException;
    elseif($proposal->status != MarriageEntity::STATUS_PROPOSED) throw new MarriageProposalAlreadyHandledException;
    elseif(!$this->canPropose($proposal->user1->id)) throw new CannotProposeMarriageException;
    $proposal->status = MarriageEntity::STATUS_ACCEPTED;
    $this->orm->marriages->persist($proposal);
    foreach($this->orm->marriages->findProposals($this->user->id) as $row) {
      if($row->id === $id) continue;
      $row->status = MarriageEntity::STATUS_DECLINED;
      $this->orm->marriages->persist($row);
    }
    $this->orm->marriages->flush();
  }
  
  /**
   * Decline a marriage proposal
   * 
   * @param int $id
   * @return void
   * @throws AuthenticationNeededException
   * @throws MarriageNotFoundException
   * @throws AccessDeniedException
   * @throws CannotProposeMarriageException
   * @throws MarriageProposalAlreadyHandledException
   */
  function declineProposal($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $proposal = $this->orm->marriages->getById($id);
    if(!$proposal) throw new MarriageNotFoundException;
    elseif($proposal->user2->id != $this->user->id) throw new AccessDeniedException;
    elseif(!$this->canPropose($proposal->user1->id)) throw new CannotProposeMarriageException;
    elseif($proposal->status != MarriageEntity::STATUS_PROPOSED) throw new MarriageProposalAlreadyHandledException;
    $proposal->status = MarriageEntity::STATUS_DECLINED;
    $this->orm->marriages->persistAndFlush($proposal);
  }
  
  /**
   * Get user's current marriage
   * 
   * @return NULL|MarriageEntity
   * @throws AuthenticationNeededException
   */
  function getCurrentMarriage() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $marriage = $this->orm->marriages->getActiveMarriage($this->user->id)->fetch();
    if(is_null($marriage)) {
      $marriage = $this->orm->marriages->getAcceptedMarriage($this->user->id)->fetch();
    }
    return $marriage;
  }
}

class CannotProposeMarriageException extends AccessDeniedException {
  
}

class MarriageNotFoundException extends RecordNotFoundException {
  
}

class MarriageProposalAlreadyHandledException extends RecordNotFoundException {
  
}
?>