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
  /** @var \Nette\Caching\Cache */
  protected $cache;
  /** @var \Nette\Security\User */
  protected $user;
  
  /**
   * @param \Nexendrie\Orm\Model $orm
   * @param \Nette\Caching\Cache $cache
   * @param \Nette\Security\User $user
   */
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Caching\Cache $cache, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->cache = $cache;
    $this->user = $user;
  }
  
  /**
   * @return \stdClass[]
   */
  function getAllNames() {
    $names = $this->cache->load("users_names");
    if($names === NULL) {
      $users = $this->orm->users->findAll();
      foreach($users as $user) {
        $names[$user->id] = (object) array(
          "id" => $user->id, "username" => $user->username, "publicname" => $user->publicname
        );
      }
      $this->cache->save("users_names", $names);
    }
    return $names;
  }
  
  /**
   * Get specified user's username and public name
   * 
   * @param int $id User's id
   * @return \stdClass
   */
  function getNames($id) {
    $names = $this->getAllNames();
    $user = Arrays::get($names, $id, false);
    return $user;
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
    $return = array();
    $lords = $this->orm->users->findBy(array("this->group->level>=" => 350));
    foreach($lords as $lord) {
      $return[$lord->id] = $lord->publicname;
    }
    return $return;
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
}

class UserNotFoundException extends RecordNotFoundException {
  
}
?>