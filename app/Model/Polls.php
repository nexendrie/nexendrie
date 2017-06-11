<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Poll as PollEntity,
    Nextras\Orm\Collection\ICollection;

/**
 * Polls Model
 *
 * @author Jakub Konečný
 * @property \Nette\Security\User $user
 */
class Polls {
  /** @var \Nexendrie\Orm\Model $orm */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  use \Nette\SmartObject;
  
  /**
   * @param \Nexendrie\Orm\Model $orm
   */
  function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * @param \Nette\Security\User $user
   */
  function setUser(\Nette\Security\User $user) {
    $this->user = $user;
  }
  
  /**
   * Get list of all polls
   * 
   * @return PollEntity[]|ICollection
   */
  function all(): ICollection {
    return $this->orm->polls->findAll();
  }
  
  /**
   * Show specified poll
   * 
   * @param int $id Poll's id
   * @return PollEntity
   * @throws PollNotFoundException
   */
  function view(int $id): PollEntity {
    $poll = $this->orm->polls->getById($id);
    if(is_null($poll)) {
      throw new PollNotFoundException("Specified poll does not exist.");
    }
    return $poll;
  }
  
  /**
   * Add poll
   * 
   * @param array $data
   * @return void
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   */
  function add(array $data): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException("This action requires authentication.");
    }
    if(!$this->user->isAllowed("poll", "add")) {
      throw new MissingPermissionsException("You don't have permissions for adding news.");
    }
    $poll = new PollEntity;
    $this->orm->polls->attach($poll);
    foreach($data as $key => $value) {
      $poll->$key = $value;
    }
    $poll->author = $this->user->id;
    $this->orm->polls->persistAndFlush($poll);
  }
  
  /**
   * Check whetever specified poll exists
   * 
   * @param int $id News' id
   * @return bool
   */
  function exists(int $id): bool {
    return (bool) $this->orm->polls->getById($id);
  }
  
  /**
   * Edit specified poll
   * 
   * @param int $id Poll's id
   * @param array $data
   * @return void
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws PollNotFoundException
   */
  function edit(int $id, array $data): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException("This action requires authentication.");
    }
    if(!$this->user->isAllowed("poll", "add")) {
      throw new MissingPermissionsException("You don't have permissions for editing polls.");
    }
    $poll = $this->orm->polls->getById($id);
    if(is_null($poll)) {
      throw new PollNotFoundException("Specified poll does not exist.");
    }
    foreach($data as $key => $value) {
      $poll->$key = $value;
    }
    $this->orm->polls->persistAndFlush($poll);
  }
}
?>