<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\UserItem as UserItemEntity;
use Nexendrie\Orm\Item as ItemEntity;
use Nexendrie\Orm\ItemSet as ItemSetEntity;
use Nexendrie\Orm\Marriage as MarriageEntity;
use Nextras\Orm\Collection\ICollection;

/**
 * Equipment Model
 *
 * @author Jakub Konečný
 */
final class Inventory {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Show user's possessions
   *
   * @throws AuthenticationNeededException
   */
  public function possessions(): array {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $return = [];
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    $return["money"] = $user->moneyT;
    $return["items"] = $user->items->get()->findBy(["this->item->type" => ItemEntity::getCommonTypes()]);
    $return["towns"] = $user->ownedTowns;
    $return["loan"] = $this->orm->loans->getActiveLoan($this->user->id);
    return $return;
  }
  
  /**
   * Show user's equipment
   * 
   * @return UserItemEntity[]|ICollection
   * @throws AuthenticationNeededException
   */
  public function equipment(): ICollection {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    return $this->orm->userItems->findEquipment($this->user->id)->orderBy("this->item->strength");
  }
  
  /**
   * Show user's potions
   * 
   * @return UserItemEntity[]|ICollection
   * @throws AuthenticationNeededException
   */
  public function potions(): ICollection {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    return $this->orm->userItems->findByType($this->user->id, "potion");
  }
  
  /**
   * Get user's intimacy boosters
   * 
   * @return UserItemEntity[]|ICollection
   * @throws AuthenticationNeededException
   */
  public function intimacyBoosters(): ICollection {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    return $this->orm->userItems->findByType($this->user->id, "intimacy_boost");
  }
  
  /**
   * Equip an item
   *
   * @throws AuthenticationNeededException
   * @throws ItemNotFoundException
   * @throws ItemNotOwnedException
   * @throws ItemNotEquipableException
   * @throws ItemAlreadyWornException
   */
  public function equipItem(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $item = $this->orm->userItems->getById($id);
    if(is_null($item)) {
      throw new ItemNotFoundException();
    } elseif($item->user->id !== $this->user->id) {
      throw new ItemNotOwnedException();
    } elseif(!in_array($item->item->type, ItemEntity::getEquipmentTypes(), true)) {
      throw new ItemNotEquipableException();
    } elseif($item->worn) {
      throw new ItemAlreadyWornException();
    }
    $item->worn = true;
    $this->orm->userItems->persist($item);
    $items = $this->orm->userItems->findByType($this->user->id, $item->item->type);
    foreach($items as $i) {
      if($i->id === $item->id) {
        continue;
      }
      $i->worn = false;
      $this->orm->userItems->persist($i);
    }
    $this->orm->flush();
  }
  
  /**
   * Unequip an item
   *
   * @throws AuthenticationNeededException
   * @throws ItemNotFoundException
   * @throws ItemNotOwnedException
   * @throws ItemNotEquipableException
   * @throws ItemNotWornException
   */
  public function unequipItem(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $item = $this->orm->userItems->getById($id);
    if(is_null($item)) {
      throw new ItemNotFoundException();
    } elseif($item->user->id !== $this->user->id) {
      throw new ItemNotOwnedException();
    } elseif(!in_array($item->item->type, ItemEntity::getEquipmentTypes(), true)) {
      throw new ItemNotEquipableException();
    } elseif(!$item->worn) {
      throw new ItemNotWornException();
    }
    $item->worn = false;
    $this->orm->userItems->persistAndFlush($item);
  }
  
  /**
   * Drink a potion
   *
   * @throws AuthenticationNeededException
   * @throws ItemNotFoundException
   * @throws ItemNotOwnedException
   * @throws ItemNotDrinkableException
   * @throws HealingNotNeededException
   */
  public function drinkPotion(int $id): int {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $item = $this->orm->userItems->getById($id);
    if(is_null($item)) {
      throw new ItemNotFoundException();
    } elseif($item->user->id !== $this->user->id) {
      throw new ItemNotOwnedException();
    } elseif($item->item->type !== ItemEntity::TYPE_POTION) {
      throw new ItemNotDrinkableException();
    }
    if($item->user->life >= $item->user->maxLife) {
      throw new HealingNotNeededException();
    }
    $item->amount -= 1;
    $life = $item->item->strength;
    if($item->user->monastery) {
      $life += $item->user->monastery->altairLevel;
    }
    if($item->amount < 1) {
      /** @var \Nexendrie\Orm\User $user */
      $user = $this->orm->users->getById($this->user->id);
      $this->orm->userItems->remove($item);
      $user->life += $life;
      $this->orm->users->persist($user);
      $this->orm->flush();
    } else {
      $item->user->life += $item->item->strength;
      $this->orm->userItems->persistAndFlush($item);
    }
    return $life;
  }
  
  /**
   * @throws AuthenticationNeededException
   * @throws NotMarriedException
   * @throws ItemNotFoundException
   * @throws ItemNotOwnedException
   * @throws ItemNotUsableException
   * @throws MaxIntimacyReachedException
   */
  public function boostIntimacy(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $marriage = $this->orm->marriages->getActiveMarriage($this->user->id);
    if(is_null($marriage)) {
      throw new NotMarriedException();
    }
    $item = $this->orm->userItems->getById($id);
    if(is_null($item)) {
      throw new ItemNotFoundException();
    } elseif($item->user->id !== $this->user->id) {
      throw new ItemNotOwnedException();
    } elseif($item->item->type !== ItemEntity::TYPE_INTIMACY_BOOST) {
      throw new ItemNotUsableException();
    }
    if($marriage->intimacy + $item->item->strength > MarriageEntity::MAX_INTIMACY) {
      throw new MaxIntimacyReachedException();
    }
    $item->amount -= 1;
    $marriage->intimacy += $item->item->strength;
    if($item->amount < 1) {
      /** @var \Nexendrie\Orm\User $user */
      $user = $this->orm->users->getById($this->user->id);
      $this->orm->userItems->remove($item);
      $this->orm->users->persist($user);
    } else {
      $item->user->life += $item->item->strength;
      $this->orm->userItems->persist($item);
    }
    $this->orm->marriages->persist($marriage);
    $this->orm->flush();
  }
  
  public function getWeapon(int $user): ?UserItemEntity {
    return $this->orm->userItems->getWornWeapon($user);
  }
  
  public function getArmor(int $user): ?UserItemEntity {
    return $this->orm->userItems->getWornArmor($user);
  }
  
  public function getHelmet(int $user): ?UserItemEntity {
    return $this->orm->userItems->getWornHelmet($user);
  }
  
  public function getUserItemSet(int $user): ?ItemSetEntity {
    $weapon = $this->getWeapon($user);
    $armor = $this->getArmor($user);
    $helmet = $this->getHelmet($user);
    $w = (!is_null($weapon)) ? $weapon->item : null;
    $a = (!is_null($armor)) ? $armor->item : null;
    $h = (!is_null($helmet)) ? $helmet->item : null;
    return $this->orm->itemSets->getByWeaponAndArmorAndHelmet($w, $a, $h);
  }
  
  /**
   * Sell an item
   *
   * @throws AuthenticationNeededException
   * @throws ItemNotFoundException
   * @throws ItemNotOwnedException
   * @throws ItemNotForSaleException
   */
  public function sellItem(int $id): int {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $item = $this->orm->userItems->getById($id);
    if(is_null($item)) {
      throw new ItemNotFoundException();
    } elseif($item->user->id !== $this->user->id) {
      throw new ItemNotOwnedException();
    } elseif(in_array($item->item->type, ItemEntity::getNotForSale(), true)) {
      throw new ItemNotForSaleException();
    }
    $item->amount -= 1;
    $price = $item->sellPrice;
    $item->user->money += $price;
    if($item->amount > 0) {
      $this->orm->userItems->persistAndFlush($item);
    } else {
      $this->orm->users->persist($item->user);
      $this->orm->userItems->remove($item);
      $this->orm->flush();
    }
    return $price;
  }
  
  /**
   * Upgrade an item
   *
   * @throws AuthenticationNeededException
   * @throws ItemNotFoundException
   * @throws ItemNotOwnedException
   * @throws ItemNotUpgradableException
   * @throws ItemMaxLevelReachedException
   * @throws InsufficientFundsException
   */
  public function upgradeItem(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $item = $this->orm->userItems->getById($id);
    if(is_null($item)) {
      throw new ItemNotFoundException();
    } elseif($item->user->id !== $this->user->id) {
      throw new ItemNotOwnedException();
    } elseif(!in_array($item->item->type, ItemEntity::getEquipmentTypes(), true)) {
      throw new ItemNotUpgradableException();
    } elseif($item->level >= $item->maxLevel) {
      throw new ItemMaxLevelReachedException();
    } elseif($item->user->money < $item->upgradePrice) {
      throw new InsufficientFundsException();
    }
    $item->user->money -= $item->upgradePrice;
    $item->level++;
    $this->orm->userItems->persistAndFlush($item);
  }
}
?>