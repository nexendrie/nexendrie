<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Marriage as MarriageEntity;
use Nextras\Orm\Collection\ICollection;

/**
 * Marriage Model
 *
 * @author Jakub Konečný
 */
final class Marriage {
  protected \Nexendrie\Orm\Model $orm;
  protected \Nette\Security\User $user;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all marriages
   * 
   * @return MarriageEntity[]|ICollection
   */
  public function listOfMarriages(): ICollection {
    return $this->orm->marriages->findAll();
  }
  
  /**
   * Get marriage
   *
   * @throws MarriageNotFoundException
   */
  public function getMarriage(int $id): MarriageEntity {
    $marriage = $this->orm->marriages->getById($id);
    if($marriage === null) {
      throw new MarriageNotFoundException();
    }
    return $marriage;
  }
  
  /**
   * Check whether the user can propose someone
   */
  public function canPropose(int $id): bool {
    if(!$this->user->isLoggedIn()) {
      return false;
    } elseif($id === $this->user->id) {
      return false;
    } elseif($id === 0) {
      return false;
    }
    $user = $this->orm->users->getById($id);
    if($user === null) {
      return false;
    } elseif($this->orm->marriages->getActiveMarriage($id) !== null) {
      return false;
    } elseif($this->orm->marriages->getAcceptedMarriage($id) !== null) {
      return false;
    }
    /** @var \Nexendrie\Orm\User $me */
    $me = $this->orm->users->getById($this->user->id);
    if($this->orm->marriages->getActiveMarriage($this->user->id) !== null) {
      return false;
    } elseif($this->orm->marriages->getAcceptedMarriage($this->user->id) !== null) {
      return false;
    } elseif($user->group->path !== $me->group->path) {
      return false;
    }
    return true;
  }
  
  public function canFinish(MarriageEntity $marriage): bool {
    if($marriage->status !== MarriageEntity::STATUS_ACCEPTED) {
      return false;
    } elseif($this->orm->marriages->getActiveMarriage($marriage->user1->id) !== null) {
      return false;
    } elseif($this->orm->marriages->getActiveMarriage($marriage->user2->id) !== null) {
      return false;
    } elseif($marriage->user1->group->path !== $marriage->user2->group->path) {
      return false;
    }
    return true;
  }
  
  /**
   * Propose someone marriage
   *
   * @throws CannotProposeMarriageException
   */
  public function proposeMarriage(int $id): void {
    if(!$this->canPropose($id)) {
      throw new CannotProposeMarriageException();
    }
    $marriage = new MarriageEntity();
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
  public function listOfProposals(): ICollection {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    return $this->orm->marriages->findProposals($this->user->id);
  }
  
  /**
   * Accept a marriage proposal
   *
   * @throws AuthenticationNeededException
   * @throws MarriageNotFoundException
   * @throws AccessDeniedException
   * @throws CannotProposeMarriageException
   * @throws MarriageProposalAlreadyHandledException
   */
  public function acceptProposal(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $proposal = $this->orm->marriages->getById($id);
    if($proposal === null) {
      throw new MarriageNotFoundException();
    } elseif($proposal->user2->id !== $this->user->id) {
      throw new AccessDeniedException();
    } elseif($proposal->status !== MarriageEntity::STATUS_PROPOSED) {
      throw new MarriageProposalAlreadyHandledException();
    } elseif(!$this->canPropose($proposal->user1->id)) {
      throw new CannotProposeMarriageException();
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
   * @throws AuthenticationNeededException
   * @throws MarriageNotFoundException
   * @throws AccessDeniedException
   * @throws CannotProposeMarriageException
   * @throws MarriageProposalAlreadyHandledException
   */
  public function declineProposal(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $proposal = $this->orm->marriages->getById($id);
    if($proposal === null) {
      throw new MarriageNotFoundException();
    } elseif($proposal->user2->id !== $this->user->id) {
      throw new AccessDeniedException();
    } elseif($proposal->status !== MarriageEntity::STATUS_PROPOSED) {
      throw new MarriageProposalAlreadyHandledException();
    } elseif(!$this->canPropose($proposal->user1->id)) {
      throw new CannotProposeMarriageException();
    }
    $proposal->status = MarriageEntity::STATUS_DECLINED;
    $this->orm->marriages->persistAndFlush($proposal);
  }
  
  /**
   * Get user's current marriage
   *
   * @throws AuthenticationNeededException
   */
  public function getCurrentMarriage(): ?MarriageEntity {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $marriage = $this->orm->marriages->getActiveMarriage($this->user->id);
    if($marriage === null) {
      $marriage = $this->orm->marriages->getAcceptedMarriage($this->user->id);
    }
    return $marriage;
  }
  
  /**
   * Cancel wedding
   *
   * @throws AuthenticationNeededException
   * @throws NotEngagedException
   * @throws WeddingAlreadyHappenedException
   */
  public function cancelWedding(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $marriage = $this->orm->marriages->getAcceptedMarriage($this->user->id);
    if($marriage === null) {
      throw new NotEngagedException();
    }
    if($marriage->term < time()) {
      throw new WeddingAlreadyHappenedException();
    }
    $marriage->status = MarriageEntity::STATUS_CANCELLED;
    $this->orm->marriages->persistAndFlush($marriage);
  }
  
  /**
   * File for divorce
   *
   * @throws AuthenticationNeededException
   * @throws NotMarriedException
   * @throws AlreadyInDivorceException
   */
  public function fileForDivorce(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $marriage = $this->orm->marriages->getActiveMarriage($this->user->id);
    if($marriage === null) {
      throw new NotMarriedException();
    } elseif($marriage->divorce) {
      throw new AlreadyInDivorceException();
    }
    $marriage->divorce = ($marriage->user1->id === $this->user->id) ? 1 : 2;
    $this->orm->marriages->persistAndFlush($marriage);
  }
  
  /**
   * Accept divorce
   *
   * @throws AuthenticationNeededException
   * @throws NotMarriedException
   * @throws NotInDivorceException
   */
  public function acceptDivorce(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $marriage = $this->orm->marriages->getActiveMarriage($this->user->id);
    if($marriage === null) {
      throw new NotMarriedException();
    } elseif($marriage->divorce < 1 || $marriage->divorce > 2) {
      throw new NotInDivorceException();
    }
    $marriage->status = MarriageEntity::STATUS_CANCELLED;
    $this->orm->marriages->persistAndFlush($marriage);
  }
  
  /**
   * Decline divorce
   *
   * @throws AuthenticationNeededException
   * @throws NotMarriedException
   * @throws NotInDivorceException
   */
  public function declineDivorce(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $marriage = $this->orm->marriages->getActiveMarriage($this->user->id);
    if($marriage === null) {
      throw new NotMarriedException();
    } elseif($marriage->divorce < 1 || $marriage->divorce > 2) {
      throw new NotInDivorceException();
    }
    $marriage->divorce += 2;
    $this->orm->marriages->persistAndFlush($marriage);
  }
  
  /**
   * Take back divorce
   *
   * @throws AuthenticationNeededException
   * @throws NotMarriedException
   * @throws NotInDivorceException
   * @throws CannotTakeBackDivorceException
   */
  public function takeBackDivorce(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $marriage = $this->orm->marriages->getActiveMarriage($this->user->id);
    if($marriage === null) {
      throw new NotMarriedException();
    } elseif($marriage->divorce < 1 || $marriage->divorce > 4) {
      throw new NotInDivorceException();
    } elseif(($marriage->divorce === 3 && $this->user->id !== $marriage->user1->id) || ($marriage->divorce === 4 && $this->user->id !== $marriage->user2->id)) {
      throw new CannotTakeBackDivorceException();
    }
    $marriage->divorce = 0;
    $this->orm->marriages->persistAndFlush($marriage);
  }
}
?>