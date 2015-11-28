<?php
namespace Nexendrie\Model;

use Nette\Utils\Arrays,
    Nexendrie\Orm\User as UserEntity;

/**
 * Profile Model
 *
 * @author Jakub Konečný
 */
class Profile extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  /**
   * @param \Nexendrie\Orm\Model $orm
   * @param \Nette\Security\User $user
   */
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Show user's profile
   * 
   * @param string $username
   * @return UserEntity
   * @throws UserNotFoundException
   */
  function view($username) {
    $user = $this->orm->users->getByUsername($username);
    if(!$user) throw new UserNotFoundException("Specified user does not exist.");
    else return $user;
  }
  
  /**
   * Get list of potential town owners
   * 
   * @return string[]
   */
  function getListOfLords() {
    return $this->orm->users->findBy(array("this->group->level>=" => 350))
      ->fetchPairs("id", "publicname");
  }
  
  /**
   * Get specified user's life
   * 
   * @param int $id  
   * @return int[]
   */
  function userLife($id = 0) {
    if($id === 0) $id = $this->user->id;
    $user = $this->orm->users->getById($id);
    if(!$user) throw new UserNotFoundException;
    else return array($user->life, $user->maxLife);
  }
  
  /**
   * Get specified user's path
   * 
   * @param int $id  
   * @return string
   */
  function getPath($id = 0) {
    if($id === 0) $id = $this->user->id;
    $user = $this->orm->users->getById($id);
    if(!$user) throw new UserNotFoundException;
    else return $user->group->path;
  }
}

class UserNotFoundException extends RecordNotFoundException {
  
}
?>