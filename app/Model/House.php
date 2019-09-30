<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\House as HouseEntity;
use Nexendrie\Orm\BeerProduction;
use Nexendrie\Orm\Group as GroupEntity;

/**
 * House Model
 *
 * @author Jakub Konečný
 */
final class House {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  protected $price;
  /** @var int */
  protected $criticalCondition;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user, SettingsRepository $sr) {
    $this->orm = $orm;
    $this->user = $user;
    $this->price = $sr->settings["fees"]["buyHouse"];
    $this->criticalCondition = $sr->settings["buildings"]["criticalCondition"];
  }
  
  /**
   * Get specified user's house
   */
  public function getUserHouse(int $user = null): ?HouseEntity {
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
  public function buyHouse(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif($this->getUserHouse() !== null) {
      throw new CannotBuyMoreHousesException();
    }
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path !== GroupEntity::PATH_CITY) {
      throw new CannotBuyHouseException();
    } elseif($user->money < $this->price) {
      throw new InsufficientFundsException();
    }
    $user->money -= $this->price;
    $house = new HouseEntity();
    $house->owner = $user;
    $this->orm->houses->persistAndFlush($house);
  }
  
  /**
   * Check whether the user can upgrade house
   *
   * @throws AuthenticationNeededException
   */
  public function canUpgrade(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $house = $this->getUserHouse();
    if($house === null) {
      return false;
    }
    return ($house->luxuryLevel < HouseEntity::MAX_LEVEL);
  }
  
  /**
   * Upgrade house
   *
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeHouseException
   * @throws InsufficientFundsException
   */
  public function upgrade(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif(!$this->canUpgrade()) {
      throw new CannotUpgradeHouseException();
    }
    /** @var HouseEntity $house */
    $house = $this->getUserHouse();
    if($house->owner->money < $house->upgradePrice) {
      throw new InsufficientFundsException();
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
  public function canRepair(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $house = $this->getUserHouse();
    if($house === null) {
      return false;
    }
    return ($house->hp < 100);
  }
  
  /**
   * Repair house
   *
   * @throws AuthenticationNeededException
   * @throws CannotRepairHouseException
   * @throws InsufficientFundsException
   */
  public function repair(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif(!$this->canRepair()) {
      throw new CannotRepairHouseException();
    }
    /** @var HouseEntity $house */
    $house = $this->getUserHouse();
    if($house->owner->money < $house->repairPrice) {
      throw new InsufficientFundsException();
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
  public function canUpgradeBrewery(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $house = $this->getUserHouse();
    if($house === null) {
      return false;
    }
    return ($house->breweryLevel < HouseEntity::MAX_LEVEL);
  }
  
  /**
   * Upgrade house
   * 
   * @return int New level
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeBreweryException
   * @throws InsufficientFundsException
   */
  public function upgradeBrewery(): int {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif(!$this->canUpgradeBrewery()) {
      throw new CannotUpgradeBreweryException();
    }
    /** @var HouseEntity $house */
    $house = $this->getUserHouse();
    if($house->owner->money < $house->breweryUpgradePrice) {
      throw new InsufficientFundsException();
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
  public function canProduceBeer(): bool {
    $sevenDays = 60 * 60 * 24 * 7;
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $house = $this->getUserHouse();
    if($house === null) {
      return false;
    } elseif($house->owner->group->path !== GroupEntity::PATH_CITY) {
      return false;
    } elseif($house->breweryLevel < 1) {
      return false;
    } elseif($house->hp < $this->criticalCondition) {
      return false;
    }
    $lastProduction = $this->orm->beerProduction->getLastProduction($house->id);
    if($lastProduction === null) {
      return true;
    }
    return ($lastProduction->when + $sevenDays < time());
  }
  
  /**
   * Produce beer
   * 
   * @return int[]
   * @throws AuthenticationNeededException
   * @throws CannotProduceBeerException
   */
  public function produceBeer(): array {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif(!$this->canProduceBeer()) {
      throw new CannotProduceBeerException();
    }
    /** @var HouseEntity $house */
    $house = $this->getUserHouse();
    $production = new BeerProduction();
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