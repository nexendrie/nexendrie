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
    if(!$user) {
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
   * Get specified user's life
   * 
   * @param int $id  
   * @return int[]
   */
  function userLife(int $id = 0): array {
    if($id === 0) {
      $id = $this->user->id;
    }
    $user = $this->orm->users->getById($id);
    if(!$user) {
      throw new UserNotFoundException;
    } else {
      return [$user->life, $user->maxLife];
    }
  }
  
  /**
   * Get specified user's path
   * 
   * @param int $id  
   * @return string
   */
  function getPath(int $id = 0): string {
    if($id === 0) {
      $id = $this->user->id;
    }
    $user = $this->orm->users->getById($id);
    if(!$user) {
      throw new UserNotFoundException;
    } else {
      return $user->group->path;
    }
  }
  
  /**
   * Get amount of completed adventures of specified user
   * 
   * @param int $id  
   * @return int
   */
  function countCompletedAdventures(int $id = 0): int {
    return $this->orm->userAdventures->findUserCompletedAdventures($id)->countStored();
  }
  
  /**
   * Get amount of beers produced by specified user
   * 
   * @param int $id  
   * @return int
   */
  function countProducedBeers(int $id = 0): int {
    $amount = 0;
    $production = $this->orm->beerProduction->findByUser($id);
    foreach($production as $row) {
      $amount += $row->amount;
    }
    return $amount;
  }
  
  /**
   * Get amount of punishments of specified user
   * 
   * @param int $id  
   * @return int
   */
  function countPunishments(int $id = 0): int {
    return $this->orm->punishments->findByUser($id)->countStored();
  }
  
  /**
   * Get amount of taken lessons of specified user
   * 
   * @param int $id  
   * @return int
   */
  function countLessons(int $id = 0): int {
    $amount = 0;
    $lessons = $this->orm->userSkills->findByUser($id);
    foreach($lessons as $lesson) {
      $amount += $lesson->level;
    }
    return $amount;
  }
  
  /**
   * Get amount of sent and received of specified user
   * 
   * @param int $id  
   * @return int[]
   */
  function countMessages(int $id = 0): array {
    return ["sent" => $this->orm->messages->findByFrom($id)->countStored(), "recieved" => $this->orm->messages->findByTo($id)->countStored()];
  }
   
   
   /**
    * Get specified user's partner
    * 
    * @param int $id 
    * @return UserEntity|NULL
    */
  function getPartner(int $id) {
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
  function getFiance(int $id) {
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