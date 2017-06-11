<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Marriage as MarriageEntity,
    Nextras\Orm\Collection\ICollection;

/**
 * Marriage Model
 *
 * @author Jakub Konečný
 */
class Marriage {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  use \Nette\SmartObject;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all marriages
   * 
   * @return MarriageEntity[]|ICollection
   */
  function listOfMarriages(): ICollection {
    return $this->orm->marriages->findAll();
  }
  
  /**
   * Get marriage
   * 
   * @param int $id
   * @return MarriageEntity
   * @throws MarriageNotFoundException
   */
  function getMarriage(int $id): MarriageEntity {
    $marriage = $this->orm->marriages->getById($id);
    if(is_null($marriage)) {
      throw new MarriageNotFoundException;
    }
    return $marriage;
  }
  
  /**
   * Check whetever the user can propose someone
   *  
   * @param int $id
   * @return bool
   */
  function canPropose(int $id): bool {
    if(!$this->user->isLoggedIn()) {
      return false;
    } elseif($id === $this->user->id) {
      return false;
    } elseif($id === 0) {
      return false;
    }
    $user = $this->orm->users->getById($id);
    if(is_null($user)) {
      return false;
    } elseif(!is_null($this->orm->marriages->getActiveMarriage($id))) {
      return false;
    } elseif(!is_null($this->orm->marriages->getAcceptedMarriage($id))) {
      return false;
    }
    $me = $this->orm->users->getById($this->user->id);
    if(!is_null($this->orm->marriages->getActiveMarriage($this->user->id))) {
      return false;
    } elseif(!is_null($this->orm->marriages->getAcceptedMarriage($this->user->id))) {
      return false;
    } elseif($user->group->path != $me->group->path) {
      return false;
    }
    return true;
  }
  
  /**
   * @param MarriageEntity $marriage
   * @return bool
   */
  function canFinish(MarriageEntity $marriage): bool {
    if($marriage->status != MarriageEntity::STATUS_ACCEPTED) {
      return false;
    } elseif(!is_null($this->orm->marriages->getActiveMarriage($marriage->user1->id))) {
      return false;
    } elseif(!is_null($this->orm->marriages->getActiveMarriage($marriage->user2->id))) {
      return false;
    } elseif($marriage->user1->group->path != $marriage->user2->group->path) {
      return false;
    }
    return true;
  }
  
  /**
   * Propose someone marriage
   * 
   * @param int $id
   * @return void
   * @throws CannotProposeMarriageException
   */
  function proposeMarriage(int $id): void {
    if(!$this->canPropose($id)) {
      throw new CannotProposeMarriageException;
    }
    $marriage = new MarriageEntity;
    $this->orm->marriages->attach($marriage);
    $marriage->user1 = $this->user->id;
    $marriage->user2 = $id;
    $this->orm->marriages->persistAndFlush($marriage);
  }
  
  /**
   * Get proposals for a user
   * 
   * @return MarriageEntity[]|ICollection
   * @throws AuthenticationNeededException
   */
  function listOfProposals(): ICollection {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    return $this->orm->marriages->findProposals($this->user->id);
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
  function acceptProposal(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $proposal = $this->orm->marriages->getById($id);
    if(is_null($proposal)) {
      throw new MarriageNotFoundException;
    } elseif($proposal->user2->id != $this->user->id) {
      throw new AccessDeniedException;
    } elseif($proposal->status != MarriageEntity::STATUS_PROPOSED) {
      throw new MarriageProposalAlreadyHandledException;
    } elseif(!$this->canPropose($proposal->user1->id)) {
      throw new CannotProposeMarriageException;
    }
    $proposal->status = MarriageEntity::STATUS_ACCEPTED;
    $this->orm->marriages->persist($proposal);
    foreach($this->orm->marriages->findProposals($this->user->id) as $row) {
      if($row->id === $id) {
        continue;
      }
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
  function declineProposal(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $proposal = $this->orm->marriages->getById($id);
    if(is_null($proposal)) {
      throw new MarriageNotFoundException;
    } elseif($proposal->user2->id != $this->user->id) {
      throw new AccessDeniedException;
    } elseif($proposal->status != MarriageEntity::STATUS_PROPOSED) {
      throw new MarriageProposalAlreadyHandledException;
    } elseif(!$this->canPropose($proposal->user1->id)) {
      throw new CannotProposeMarriageException;
    }
    $proposal->status = MarriageEntity::STATUS_DECLINED;
    $this->orm->marriages->persistAndFlush($proposal);
  }
  
  /**
   * Get user's current marriage
   * 
   * @return MarriageEntity|NULL
   * @throws AuthenticationNeededException
   */
  function getCurrentMarriage(): ?MarriageEntity {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $marriage = $this->orm->marriages->getActiveMarriage($this->user->id);
    if(is_null($marriage)) {
      $marriage = $this->orm->marriages->getAcceptedMarriage($this->user->id);
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
  function cancelWedding(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $marriage = $this->orm->marriages->getAcceptedMarriage($this->user->id);
    if(is_null($marriage)) {
      throw new NotEngagedException;
    }
    if($marriage->term < time()) {
      throw new WeddingAlreadyHappenedException;
    }
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
  function fileForDivorce(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $marriage = $this->orm->marriages->getActiveMarriage($this->user->id);
    if(is_null($marriage)) {
      throw new NotMarriedException;
    } elseif($marriage->divorce) {
      throw new AlreadyInDivorceException;
    }
    $marriage->divorce = ($marriage->user1->id === $this->user->id) ? 1 : 2;
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
  function acceptDivorce(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $marriage = $this->orm->marriages->getActiveMarriage($this->user->id);
    if(is_null($marriage)) {
      throw new NotMarriedException;
    } elseif($marriage->divorce < 1 OR $marriage->divorce > 2) {
      throw new NotInDivorceException;
    }
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
  function declineDivorce(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $marriage = $this->orm->marriages->getActiveMarriage($this->user->id);
    if(is_null($marriage)) {
      throw new NotMarriedException;
    } elseif($marriage->divorce < 1 OR $marriage->divorce > 2) {
      throw new NotInDivorceException;
    }
    $marriage->divorce += 2;
    $this->orm->marriages->persistAndFlush($marriage);
  }
  
  /**
   * Take back divorce
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws NotMarriedException
   * @throws NotInDivorceException
   * @throws CannotTakeBackDivorceException
   */
  function takeBackDivorce(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $marriage = $this->orm->marriages->getActiveMarriage($this->user->id);
    if(is_null($marriage)) {
      throw new NotMarriedException;
    } elseif($marriage->divorce < 1 OR $marriage->divorce > 4) {
      throw new NotInDivorceException;
    } elseif(($marriage->divorce === 3 AND $this->user->id != $marriage->user1->id) OR ($marriage->divorce === 4 AND $this->user->id != $marriage->user2->id)) {
      throw new CannotTakeBackDivorceException;
    }
    $marriage->divorce = 0;
    $this->orm->marriages->persistAndFlush($marriage);
  }
}
?>