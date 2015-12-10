<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\House as HouseEntity;

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
    if($user->group->path != "city") throw new CannotBuyHouse;
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
   * @throws CannotUpgradeHouseException
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
?>