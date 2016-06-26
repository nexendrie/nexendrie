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
   * Get marriage
   * 
   * @param int $id
   * @return MarriageEntity
   * @throws MarriageNotFoundException
   */
  function getMarriage($id) {
    $marriage = $this->orm->marriages->getById($id);
    if(!$marriage) throw new MarriageNotFoundException;
    else return $marriage;
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
   * @param MarriageEntity $marriage
   * @return bool
   */
  function canFinish(MarriageEntity $marriage) {
    if($marriage->status != MarriageEntity::STATUS_ACCEPTED) return false;
    elseif(!is_null($this->orm->marriages->getActiveMarriage($marriage->user1->id)->fetch())) return false;
    elseif(!is_null($this->orm->marriages->getActiveMarriage($marriage->user2->id)->fetch())) return false;
    elseif($marriage->user1->group->path != $marriage->user2->group->path) return false;
    else return true;
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
  
  /**
   * Cancel wedding
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws NotEngagedException
   * @throws WeddingAlreadyHappenedException
   */
  function cancelWedding() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $marriage = $this->orm->marriages->getAcceptedMarriage($this->user->id)->fetch();
    if(is_null($marriage)) throw new NotEngagedException;
    if($marriage->term < time()) throw new WeddingAlreadyHappenedException;
    $marriage->status = MarriageEntity::STATUS_CANCELLED;
    $this->orm->marriages->persistAndFlush($marriage);
  }
  
  /**
   * File for divorce
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws NotMarriedException
   * @throws AlreadyInDivorceException
   */
  function fileForDivorce() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $marriage = $this->orm->marriages->getActiveMarriage($this->user->id)->fetch();
    if(is_null($marriage)) throw new NotMarriedException;
    elseif($marriage->divorce) throw new AlreadyInDivorceException;
    if($marriage->user1->id === $this->user->id) $marriage->divorce = 1;
    else $marriage->divorce = 2;
    $this->orm->marriages->persistAndFlush($marriage);
  }
  
  /**
   * Accept divorce
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws NotMarriedException
   * @throws NotInDivorceException
   */
  function acceptDivorce() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $marriage = $this->orm->marriages->getActiveMarriage($this->user->id)->fetch();
    if(is_null($marriage)) throw new NotMarriedException;
    elseif($marriage->divorce < 1 OR $marriage->divorce > 2) throw new NotInDivorceException;
    $marriage->status = MarriageEntity::STATUS_CANCELLED;
    $this->orm->marriages->persistAndFlush($marriage);
  }
  
  /**
   * Decline divorce
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws NotMarriedException
   * @throws NotInDivorceException
   */
  function declineDivorce() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $marriage = $this->orm->marriages->getActiveMarriage($this->user->id)->fetch();
    if(is_null($marriage)) throw new NotMarriedException;
    elseif($marriage->divorce < 1 OR $marriage->divorce > 2) throw new NotInDivorceException;
    $marriage->divorce += 2;
    $this->orm->marriages->persistAndFlush($marriage);
  }
}

class CannotProposeMarriageException extends AccessDeniedException {
  
}

class MarriageNotFoundException extends RecordNotFoundException {
  
}

class MarriageProposalAlreadyHandledException extends RecordNotFoundException {
  
}

class NotEngagedException extends AccessDeniedException {
  
}

class NotMarriedException extends AccessDeniedException {
  
}

class WeddingAlreadyHappenedException extends AccessDeniedException {
  
}

class AlreadyInDivorceException extends AccessDeniedException {
  
}

class NotInDivorceException extends AccessDeniedException {
  
}
?>