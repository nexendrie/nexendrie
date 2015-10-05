<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Poll as PollEntity;

/**
 * Polls Model
 *
 * @author Jakub Konečný
 */
class Polls extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model $orm */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
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
   * @return PollEntity[]
   */
  function all() {
    return $this->orm->polls->findAll();
  }
  
  /**
   * Show specified poll
   * 
   * @param int $id Poll's id
   * @return PollEntity
   * @throws PollNotFoundException
   */
  function view($id) {
    $poll = $this->orm->polls->getById($id);
    if(!$poll) throw new PollNotFoundException("Specified poll does not exist.");
    else return $poll;
  }
  
  /**
   * Add poll
   * 
   * @param \Nette\Utils\ArrayHash $data
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @return void
   */
  function add(\Nette\Utils\ArrayHash $data) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException("This action requires authentication.");
    if(!$this->user->isAllowed("poll", "add")) throw new MissingPermissionsException("You don't have permissions for adding news.");
    $poll = new PollEntity;
    $this->orm->polls->attach($poll);
    foreach($data as $key => $value) {
      $poll->$key = $value;
    }
    $poll->author = $this->user->id;
    $poll->added = time();
    $this->orm->polls->persistAndFlush($poll);
  }
  
  /**
   * Check whetever specified poll exists
   * 
   * @param int $id News' id
   * @return bool
   */
  function exists($id) {
    return (bool) $this->orm->polls->getById($id);
  }
  
  /**
   * Edit specified poll
   * 
   * @param int $id Poll's id
   * @param \Nette\Utils\ArrayHash $data
   * @return void
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws PollNotFoundException
   */
  function edit($id, \Nette\Utils\ArrayHash $data) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException("This action requires authentication.");
    if(!$this->user->isAllowed("poll", "add")) throw new MissingPermissionsException("You don't have permissions for editing polls.");
    $poll = $this->orm->polls->getById($id);
    if(!$poll) throw new PollNotFoundException("Specified news does not exist.");
    foreach($data as $key => $value) {
      $poll->$key = $value;
    }
    $this->orm->polls->persistAndFlush($poll);
  }
}

/**
 * Poll voting exception
 * 
 * @author Jakub Konečný
 */
class PollVotingException extends \Exception {
  
}

class PollNotFoundException extends RecordNotFoundException {
  
}
?>