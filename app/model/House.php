<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\House as HouseEntity,
    Nexendrie\Orm\BeerProduction,
    Nexendrie\Orm\Group as GroupEntity;

/**
 * House Model
 *
 * @author Jakub Konečný
 */
class House extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  protected $price = 500;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get specified user's house
   * 
   * @param int $user
   * @return HouseEntity|NULL
   */
  function getUserHouse($user = 0) {
    if($user === 0) $user = $this->user->id;
    return $this->orm->houses->getByOwner($user);
  }
  
  /**
   * Buy a house
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotBuyMoreHousesException
   * @throws CannotBuyHouse
   * @throws InsufficientFundsException
   */
  function buyHouse() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif($this->getUserHouse()) throw new CannotBuyMoreHousesException;
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path != GroupEntity::PATH_CITY) throw new CannotBuyHouse;
    elseif($user->money < $this->price) throw new InsufficientFundsException;
    $user->money -= $this->price;
    $house = new HouseEntity;
    $house->owner = $user;
    $this->orm->houses->persistAndFlush($house);
    $user->house = $this->orm->houses->getByOwner($user->id);
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Check whetever the user can upgrade house
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canUpgrade() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $house = $this->orm->houses->getByOwner($this->user->id);
    if(!$house) return false;
    elseif($house->luxuryLevel >= HouseEntity::MAX_LEVEL) return false;
    else return true;
  }
  
  /**
   * Upgrade house
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeHouseException
   * @throws InsufficientFundsException
   */
  function upgrade() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canUpgrade()) throw new CannotUpgradeHouseException;
    $house = $this->orm->houses->getByOwner($this->user->id);
    if($house->owner->money < $house->upgradePrice) throw new InsufficientFundsException;
    $house->owner->money -= $house->upgradePrice;
    $house->luxuryLevel++;
    $this->orm->houses->persistAndFlush($house);
  }
  
  /**
   * Check whetever the user can repair house
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canRepair() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $house = $this->orm->houses->getByOwner($this->user->id);
    if(!$house) return false;
    elseif($house->hp >= 100) return false;
    else return true;
  }
  
  /**
   * Repair house
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotRepairHouseException
   * @throws InsufficientFundsException
   */
  function repair() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canRepair()) throw new CannotRepairHouseException;
    $house = $this->orm->houses->getByOwner($this->user->id);
    if($house->owner->money < $house->repairPrice) throw new InsufficientFundsException;
    $house->owner->money -= $house->repairPrice;
    $house->hp = 100;
    $this->orm->houses->persistAndFlush($house);
  }
  
  /**
   * Check whetever the user can upgrade brewery
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canUpgradeBrewery() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $house = $this->orm->houses->getByOwner($this->user->id);
    if(!$house) return false;
    elseif($house->breweryLevel >= HouseEntity::MAX_LEVEL) return false;
    else return true;
  }
  
  /**
   * Upgrade house
   * 
   * @return int New level
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeBreweryException
   * @throws InsufficientFundsException
   */
  function upgradeBrewery() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canUpgradeBrewery()) throw new CannotUpgradeBreweryException;
    $house = $this->orm->houses->getByOwner($this->user->id);
    if($house->owner->money < $house->breweryUpgradePrice) throw new InsufficientFundsException;
    $house->owner->money -= $house->breweryUpgradePrice;
    $house->breweryLevel++;
    $this->orm->houses->persistAndFlush($house);
    return $house->breweryLevel;
  }
  
  /**
   * Check whetever the user can produce beer
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canProduceBeer() {
    $sevenDays = 60 * 60 * 24 * 7;
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $house = $this->orm->houses->getByOwner($this->user->id);
    if(!$house) return false;
    elseif($house->owner->group->path != GroupEntity::PATH_CITY) return false;
    elseif($house->breweryLevel < 1) return false;
    elseif($house->hp < 31) return false;
    $lastProduction = $this->orm->beerProduction->getLastProduction($house->id);
    if(!$lastProduction->count()) return true;
    elseif($lastProduction->fetch()->when + $sevenDays < time()) return true;
    else return false;
  }
  
  /**
   * Produce beer
   * 
   * @return int[]
   * @throws AuthenticationNeededException
   * @throws CannotProduceBeerException
   */
  function produceBeer() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canProduceBeer()) throw new CannotProduceBeerException;
    $house = $this->orm->houses->getByOwner($this->user->id);
    $production = new BeerProduction;
    $production->house = $house;
    $production->user = $house->owner;
    $production->amount = $house->breweryLevel;
    $production->price = 30;
    $house->owner->lastActive = time();
    $house->owner->money += $production->amount * $production->price;
    $this->orm->beerProduction->persistAndFlush($production);
    return array("amount" => $production->amount, "price" => $production->price);
  }
}

class CannotBuyMoreHousesException extends AccessDeniedException {
  
}

class CannotBuyHouse extends AccessDeniedException {
  
}

class CannotUpgradeHouseException extends AccessDeniedException {
  
}

class CannotRepairHouseException extends AccessDeniedException {
  
}

class CannotUpgradeBreweryException extends AccessDeniedException {
  
}

class CannotProduceBeerException extends AccessDeniedException {
  
}
?>