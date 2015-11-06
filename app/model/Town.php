<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Town as TownEntity;

/**
 * Town Model
 *
 * @author Jakub Konečný
 */
class Town extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get specified town
   * 
   * @param int $id Town's id
   * @return TownEntity
   * @throws TownNotFoundException
   */
  function get($id) {
    $town = $this->orm->towns->getById($id);
    if(!$town) throw new TownNotFoundException;
    else return $town;
  }
  
  /**
   * Get list of all towns
   * 
   * @return TownEntity[]
   */
  function listOfTowns() {
    return $this->orm->towns->findAll();
  }
  
  /**
   * Add new town
   * 
   * @param array $data
   * @return void
   */
  function add(array $data) {
    $town = new TownEntity;
    $this->orm->towns->attach($town);
    foreach($data as $key => $value) {
      $town->$key = $value;
    }
    $this->orm->towns->persistAndFlush($town);
  }
  
  /**
   * Edit specified town
   * 
   * @param int $id Town's id
   * @param array $data
   * @return void
   * @throws TownNotFoundException
   */
  function edit($id, array $data) {
    try {
      $town = $this->get($id);
    } catch(TownNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      $town->$key = $value;
    }
    $this->orm->towns->persistAndFlush($town);
  }
  
  /**
   * @return TownEntity[]
   */
  function townsOnSale() {
    return $this->orm->towns->findOnMarket();
  }
  
  /**
   * Buy specified mount
   * 
   * @param int $id Mount's id
   * @return void
   * @throws AuthenticationNeededException
   * @throws TownNotFoundException
   * @throws TownNotOnSaleException
   * @throws CannotBuyOwnTownException
   * @throws InsufficientLevelForTownException
   * @throws InsufficientFundsException
   */
  function buy($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $town = $this->orm->towns->getById($id);
    if(!$town) throw new TownNotFoundException;
    if(!$town->onMarket) throw new TownNotOnSaleException;
    if($town->owner->id === $this->user->id) throw new CannotBuyOwnTownException;
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->level < 350) throw new InsufficientLevelForTownException;
    if($user->money < $town->price) throw new InsufficientFundsException;
    $seller = $town->owner;
    $seller->money += $town->price;
    $this->orm->users->persist($seller);
    $user->money -= $town->price;
    $user->lastActive = time();
    $town->owner = $user;
    $town->onMarket = false;
    $this->orm->towns->persist($town);
    $this->orm->flush();
  }
  
  /**
   * @param int $town
   * @return int|NULL
   */
  function getMayorId($town) {
    $mayor = $this->orm->users->getTownMayor($town);
    if($mayor) return $mayor->id;
    else return NULL;
  }
  
  /**
   * Get citizens of specified town
   * 
   * @param int $town
   * @return string[] id => publicname
   */
  function getTownCitizens($town) {
    return $this->orm->users->findTownCitizens($town)
      ->fetchPairs("id", "publicname");
  }
  
  /**
   * Appoint new mayor of a town
   * 
   * @param int $townId
   * @param int $newMayorId
   * @return void
   * @throws AuthenticationNeededException
   * @throws TownNotFoundException
   * @throws TownNotOwnedException
   * @throws UserNotFoundException
   * @throws UserDoesNotLiveInTheTownException
   * @throws InsufficientLevelForMayorException
   */
  function appointMayor($townId, $newMayorId) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $town = $this->orm->towns->getById($townId);
    if(!$town) throw new TownNotFoundException;
    elseif($town->owner->id != $this->user->id) throw new TownNotOwnedException;
    $newMayor = $this->orm->users->getById($newMayorId);
    if(!$newMayor) throw new UserNotFoundException;
    elseif($newMayor->town->id != $townId) throw new UserDoesNotLiveInTheTownException;
    $newMayorRank = $newMayor->group->level;
    if(!in_array($newMayorRank, array(100, 300))) throw new InsufficientLevelForMayorException;
    $oldMayor = $this->orm->users->getTownMayor($townId);
    if($oldMayor) {
      $oldMayor->group = $this->orm->groups->getByLevel(100);
      $this->orm->users->persist($oldMayor);
    }
    $newMayor->group = $this->orm->groups->getByLevel(345);
    $this->orm->users->persistAndFlush($newMayor);
  }
}

class TownNotFoundException extends RecordNotFoundException {
  
}

class TownNotOnSaleException extends AccessDeniedException {
  
}

class InsufficientLevelForTownException extends AccessDeniedException {
  
}

class CannotBuyOwnTownException extends AccessDeniedException {
  
}

class TownNotOwnedException extends AccessDeniedException {
  
}

class InsufficientLevelForMayorException extends AccessDeniedException {
  
}

class UserDoesNotLiveInTheTownException extends AccessDeniedException {
  
}
?>