<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\House as HouseEntity,
    Nexendrie\Orm\BeerProduction,
    Nexendrie\Orm\Group as GroupEntity;

/**
 * House Model
 *
 * @author Jakub Konečný
 */
class House {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  protected $price = 500;
  
  use \Nette\SmartObject;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get specified user's house
   */
  function getUserHouse(int $user = NULL): ?HouseEntity {
    return $this->orm->houses->getByOwner($user ?? $this->user->id);
  }
  
  /**
   * Buy a house
   *
   * @throws AuthenticationNeededException
   * @throws CannotBuyMoreHousesException
   * @throws CannotBuyHouseException
   * @throws InsufficientFundsException
   */
  function buyHouse(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    } elseif($this->getUserHouse()) {
      throw new CannotBuyMoreHousesException;
    }
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path != GroupEntity::PATH_CITY) {
      throw new CannotBuyHouseException;
    } elseif($user->money < $this->price) {
      throw new InsufficientFundsException;
    }
    $user->money -= $this->price;
    $house = new HouseEntity;
    $house->owner = $user;
    $this->orm->houses->persistAndFlush($house);
    $user->house = $this->orm->houses->getByOwner($user->id);
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Check whether the user can upgrade house
   *
   * @throws AuthenticationNeededException
   */
  function canUpgrade(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $house = $this->getUserHouse();
    if(is_null($house)) {
      return false;
    } elseif($house->luxuryLevel >= HouseEntity::MAX_LEVEL) {
      return false;
    }
    return true;
  }
  
  /**
   * Upgrade house
   *
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeHouseException
   * @throws InsufficientFundsException
   */
  function upgrade(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    } elseif(!$this->canUpgrade()) {
      throw new CannotUpgradeHouseException;
    }
    $house = $this->getUserHouse();
    if($house->owner->money < $house->upgradePrice) {
      throw new InsufficientFundsException;
    }
    $house->owner->money -= $house->upgradePrice;
    $house->luxuryLevel++;
    $this->orm->houses->persistAndFlush($house);
  }
  
  /**
   * Check whether the user can repair house
   *
   * @throws AuthenticationNeededException
   */
  function canRepair(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $house = $this->getUserHouse();
    if(is_null($house)) {
      return false;
    } elseif($house->hp >= 100) {
      return false;
    }
    return true;
  }
  
  /**
   * Repair house
   *
   * @throws AuthenticationNeededException
   * @throws CannotRepairHouseException
   * @throws InsufficientFundsException
   */
  function repair(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    } elseif(!$this->canRepair()) {
      throw new CannotRepairHouseException;
    }
    $house = $this->getUserHouse();
    if($house->owner->money < $house->repairPrice) {
      throw new InsufficientFundsException;
    }
    $house->owner->money -= $house->repairPrice;
    $house->hp = 100;
    $this->orm->houses->persistAndFlush($house);
  }
  
  /**
   * Check whether the user can upgrade brewery
   *
   * @throws AuthenticationNeededException
   */
  function canUpgradeBrewery(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $house = $this->getUserHouse();
    if(is_null($house)) {
      return false;
    } elseif($house->breweryLevel >= HouseEntity::MAX_LEVEL) {
      return false;
    }
    return true;
  }
  
  /**
   * Upgrade house
   * 
   * @return int New level
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeBreweryException
   * @throws InsufficientFundsException
   */
  function upgradeBrewery(): int {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    } elseif(!$this->canUpgradeBrewery()) {
      throw new CannotUpgradeBreweryException;
    }
    $house = $this->getUserHouse();
    if($house->owner->money < $house->breweryUpgradePrice) {
      throw new InsufficientFundsException;
    }
    $house->owner->money -= $house->breweryUpgradePrice;
    $house->breweryLevel++;
    $this->orm->houses->persistAndFlush($house);
    return $house->breweryLevel;
  }
  
  /**
   * Check whether the user can produce beer
   *
   * @throws AuthenticationNeededException
   */
  function canProduceBeer(): bool {
    $sevenDays = 60 * 60 * 24 * 7;
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $house = $this->getUserHouse();
    if(is_null($house)) {
      return false;
    } elseif($house->owner->group->path != GroupEntity::PATH_CITY) {
      return false;
    } elseif($house->breweryLevel < 1) {
      return false;
    } elseif($house->hp < 31) {
      return false;
    }
    $lastProduction = $this->orm->beerProduction->getLastProduction($house->id);
    if(is_null($lastProduction)) {
      return true;
    } elseif($lastProduction->when + $sevenDays < time()) {
      return true;
    }
    return false;
  }
  
  /**
   * Produce beer
   * 
   * @return int[]
   * @throws AuthenticationNeededException
   * @throws CannotProduceBeerException
   */
  function produceBeer(): array {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    } elseif(!$this->canProduceBeer()) {
      throw new CannotProduceBeerException;
    }
    $house = $this->getUserHouse();
    $production = new BeerProduction;
    $production->house = $house;
    $production->user = $house->owner;
    $production->amount = $house->breweryLevel;
    $production->price = 30;
    $house->owner->lastActive = time();
    $house->owner->money += $production->amount * $production->price;
    $this->orm->beerProduction->persistAndFlush($production);
    return ["amount" => $production->amount, "price" => $production->price];
  }
}
?>