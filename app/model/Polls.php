<?php
namespace Nexendrie\Model;

/**
 * Polls Model
 *
 * @author Jakub Konečný
 */
class Polls extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model $orm */
  protected $orm;
  /** @var \Nexendrie\Model\Profile */
  protected $profileModel;
  /** @var \Nette\Security\User */
  protected $user;
  
  /**
   * @param \Nexendrie\Orm\Model $orm
   * @param \Nexendrie\Model\Profile $profileModel
   */
  function __construct(\Nexendrie\Orm\Model $orm, Profile $profileModel) {
    $this->orm = $orm;
    $this->profileModel = $profileModel;
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
   * @return \stdClass[]
   */
  function all() {
    return $this->orm->polls->findAll();
  }
  
  /**
   * Show specified poll
   * 
   * @param int $id Poll's id
   * @return \Nexendrie\Orm\Poll
   * @throws \Nette\Application\BadRequestException
   */
  function view($id) {
    $poll = $this->orm->polls->getById($id);
    if(!$poll) throw new \Nette\Application\BadRequestException("Specified poll does not exist.");
    else return $poll;
  }
  
  /**
   * Add poll
   * 
   * @param \Nette\Utils\ArrayHash $data
   * @throws \Nette\Application\ForbiddenRequestException
   * @return void
   */
  function add(\Nette\Utils\ArrayHash $data) {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException ("This action requires authentication.", 401);
    if(!$this->user->isAllowed("poll", "add")) throw new \Nette\Application\ForbiddenRequestException ("You don't have permissions for adding news.", 403);
    $poll = new \Nexendrie\Orm\Poll;
    foreach($data as $key => $value) {
      $poll->$key = $value;
    }
    $poll->author = $this->orm->users->getById($this->user->id);
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
   * @throws \Nette\Application\ForbiddenRequestException
   * @throws \Nette\ArgumentOutOfRangeException
   */
  function edit($id, \Nette\Utils\ArrayHash $data) {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException ("This action requires authentication.", 401);
    if(!$this->user->isAllowed("poll", "add")) throw new \Nette\Application\ForbiddenRequestException ("You don't have permissions for editing polls.", 403);
    if(!$this->exists($id)) throw new \Nette\ArgumentOutOfRangeException("Specified news does not exist.");
    $poll = $this->orm->polls->getById($id);
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
?>