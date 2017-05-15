<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\User as UserEntity;

/**
 * Profile Model
 *
 * @author Jakub Konečný
 */
class Profile {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  use \Nette\SmartObject;
  
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
  function view(string $username): UserEntity {
    $user = $this->orm->users->getByUsername($username);
    if(is_null($user)) {
      throw new UserNotFoundException("Specified user does not exist.");
    } else {
      return $user;
    }
  }
  
  /**
   * Get list of potential town owners
   * 
   * @return string[]
   */
  function getListOfLords(): array {
    return $this->orm->users->findBy(["this->group->level>=" => 350])
      ->fetchPairs("id", "publicname");
  }
  
  /**
   * Get specified user's path
   * 
   * @param int $id  
   * @return string
   */
  function getPath(int $id = NULL): string {
    $user = $this->orm->users->getById($id ?? $this->user->id);
    if(!$user) {
      throw new UserNotFoundException;
    } else {
      return $user->group->path;
    }
  }
   
   
   /**
    * Get specified user's partner
    * 
    * @param int $id 
    * @return UserEntity|NULL
    */
  function getPartner(int $id): ?UserEntity {
    $marriage = $this->orm->marriages->getActiveMarriage($id)->fetch();
    if($marriage AND $marriage->user1->id === $id) {
      return $marriage->user2;
    } elseif($marriage AND $marriage->user2->id === $id) {
      return $marriage->user1;
    } else {
      return NULL;
    }
  }
   
   /**
    * Get specified user's fiance(e)
    * 
    * @param int $id 
    * @return UserEntity|NULL
    */
  function getFiance(int $id): ?UserEntity {
    $marriage = $this->orm->marriages->getAcceptedMarriage($id)->fetch();
    if($marriage AND $marriage->user1->id === $id) {
      return $marriage->user2;
    } elseif($marriage AND $marriage->user2->id === $id) {
      return $marriage->user1;
    } else {
      return NULL;
    }
  }
}
?>