<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Town as TownEntity,
    Nexendrie\Orm\Message as MessageEntity,
    Nexendrie\Orm\Group as GroupEntity;

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
  /** @var int */
  protected $foundingPrice = 1000;
  
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
   * @return \Nexendrie\Orm\User|NULL
   */
  function getMayor($town) {
    $mayor = $this->orm->users->getTownMayor($town);
    if($mayor) return $mayor;
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
    if(!in_array($newMayorRank, [100, 300])) throw new InsufficientLevelForMayorException;
    $oldMayor = $this->orm->users->getTownMayor($townId);
    if($oldMayor) {
      $oldMayor->group = $this->orm->groups->getByLevel(100);
      $this->orm->users->persist($oldMayor);
    }
    $newMayor->group = $this->orm->groups->getByLevel(345);
    $this->orm->users->persistAndFlush($newMayor);
  }
  
  /**
   * Check whetever the user can move to different town (now)
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canMove() {
    $month = 60 * 60 * 24 * 31;
    if(!$this->user->isLoggedIn()) return false;
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path === GroupEntity::PATH_CHURCH) return false;
    elseif($user->group->path === GroupEntity::PATH_CITY AND $user->group->level != 100) return false;
    elseif($user->lastTransfer === NULL) return true;
    elseif($user->lastTransfer + $month > time()) return false;
    elseif($user->guild AND $user->guildRank->id === 4) return false;
    else return true;
  }
  
  /**
   * Move to specified town
   * 
   * @param int $id
   * @return void
   * @throws AuthenticationNeededException
   * @throws TownNotFoundException
   * @throws CannotMoveToSameTownException
   * @throws CannotMoveToTownException
   */
  function moveToTown($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $town = $this->orm->towns->getById($id);
    if(!$town) throw new TownNotFoundException;
    $user = $this->orm->users->getById($this->user->id);
    if($id === $user->town->id) throw new CannotMoveToSameTownException;
    elseif(!$this->canMove()) throw new CannotMoveToTownException;
    $this->user->identity->town = $user->town = $id;
    $user->lastTransfer = $user->lastActive = time();
    $user->guild = $user->guildRank = NULL;
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Found new town
   * 
   * @param array $data
   * @return void
   * @throws AuthenticationNeededException
   * @throws InsufficientLevelForFoundTownException
   * @throws InsufficientFundsException
   * @throws CannotFoundTownException
   * @throws TownNameInUseException
   */
  function found(array $data) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path != GroupEntity::PATH_TOWER) throw new InsufficientLevelForFoundTownException;
    if($user->money < $this->foundingPrice) throw new InsufficientFundsException;
    $item = $this->orm->userItems->getByUserAndItem($user->id, 15);
    if(!$item) throw new CannotFoundTownException;
    if($this->orm->towns->getByName($data["name"])) throw new TownNameInUseException;
    $item->amount--;
    if($item->amount < 1) {
      $this->orm->userItems->removeAndFlush($item, false);
    }
    $town = new TownEntity;
    $town->name = $data["name"];
    $town->description = $data["description"];
    $town->owner = $user;
    $town->founded = time();
    $town->owner->money -= $this->foundingPrice;
    $town->price = $this->foundingPrice;
    $this->orm->towns->persistAndFlush($town);
  }
  
  /**
   * Get peasants from specified town
   * 
   * @param int $town
   * @return string[] id => publicname
   */
  function getTownPeasants($town) {
    return $this->orm->users->findTownPeasants($town)
      ->fetchPairs("id", "publicname");
  }
  
  /**
   * Promote a peasant to citizen
   * 
   * @param int $id
   * @return void
   * @throws AuthenticationNeededException
   * @throws UserNotFoundException
   * @throws UserDoesNotLiveInTheTownException
   * @throws TooHighLevelException
   */
  function makeCitizen($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $citizen = $this->orm->users->getById($id);
    if(!$citizen) throw new UserNotFoundException;
    $owner = $this->orm->users->getById($this->user->id);
    if($citizen->town->owner->id != $owner->id) throw new UserDoesNotLiveInTheTownException;
    elseif($citizen->group->level > 50) throw new TooHighLevelException;
    $citizen->group = $this->orm->groups->getByLevel(100);
    $message = new MessageEntity;
    $message->from = $owner;
    $message->to = $citizen;
    $message->sent = time();
    $message->subject = "Povýšení";
    $message->text = "Byl jsi povýšen na měšťana.";
    $this->orm->users->persistAndFlush($citizen);
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

class CannotMoveToSameTownException extends AccessDeniedException {
  
}

class CannotMoveToTownException extends AccessDeniedException {
  
}

class InsufficientLevelForFoundTownException extends InsufficientLevelException {
  
}

class CannotFoundTownException extends AccessDeniedException {
  
}

class TownNameInUseException extends NameInUseException {
  
}

class TooHighLevelException extends AccessDeniedException {
  
}
?>