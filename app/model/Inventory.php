<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\UserItem as UserItemEntity,
    Nexendrie\Orm\Item as ItemEntity;

/**
 * Equipment Model
 *
 * @author Jakub Konečný
 */
class Inventory extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Show user's possessions
   * 
   * @return array
   * @throws AuthenticationNeededException
   */
  function possessions() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $return = array();
    $user = $this->orm->users->getById($this->user->id);
    $return["money"] = $user->moneyT;
    $return["items"] = $user->items->get()->findBy(array("this->item->type" => ItemEntity::getCommonTypes()));
    $return["towns"] = $user->ownedTowns;
    return $return;
  }
  
  /**
   * Show user's equipment
   * 
   * @return UserItemEntity[]
   * @throws AuthenticationNeededException
   */
  function equipment() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    return $this->orm->userItems->findEquipment($this->user->id);
  }
  
  /**
   * Show user's potions
   * 
   * @return UserItemEntity[]
   * @throws AuthenticationNeededException
   */
  function potions() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    return $this->orm->userItems->findByType($this->user->id, "potion");
  }
  
  /**
   * Equip an item
   * 
   * @param int $id
   * @return void
   * @throws AuthenticationNeededException
   * @throws ItemNotFoundException
   * @throws ItemNotOwnedException
   * @throws ItemNotEquipableException
   * @throws ItemAlreadyWornException
   */
  function equipItem($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $item = $this->orm->userItems->getById($id);
    if(!$item) throw new ItemNotFoundException;
    elseif($item->user->id != $this->user->id) throw new ItemNotOwnedException;
    elseif(!in_array($item->item->type, ItemEntity::getEquipmentTypes())) throw new ItemNotEquipableException;
    elseif($item->worn) throw new ItemAlreadyWornException;
    $item->worn = true;
    $this->orm->userItems->persist($item);
    $items = $this->orm->userItems->findByType($this->user->id, $item->item->type);
    foreach($items as $i) {
      if($i->id === $item->id) continue;
      $i->worn = false;
      $this->orm->userItems->persist($i);
    }
    $this->orm->flush();
  }
  
  /**
   * Unequip an item
   * 
   * @param int $id
   * @return void
   * @throws AuthenticationNeededException
   * @throws ItemNotFoundException
   * @throws ItemNotOwnedException
   * @throws ItemNotEquipableException
   * @throws ItemNotWornException
   */
  function unequipItem($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $item = $this->orm->userItems->getById($id);
    if(!$item) throw new ItemNotFoundException;
    elseif($item->user->id != $this->user->id) throw new ItemNotOwnedException;
    elseif($item->item->type === "item" OR $item->item->type === "potion") throw new ItemNotEquipableException;
    elseif(!$item->worn) throw new ItemNotWornException;
    $item->worn = false;
    $this->orm->userItems->persistAndFlush($item);
  }
  
  /**
   * Drink a potion
   * 
   * @param int $id
   * @return int
   * @throws AuthenticationNeededException
   * @throws ItemNotFoundException
   * @throws ItemNotOwnedException
   * @throws ItemNotDrinkableException
   * @throws HealingNotNeeded
   */
  function drinkPotion($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $item = $this->orm->userItems->getById($id);
    if(!$item) throw new ItemNotFoundException;
    elseif($item->user->id != $this->user->id) throw new ItemNotOwnedException;
    elseif($item->item->type != "potion") throw new ItemNotDrinkableException;
    if($item->user->life >= $item->user->maxLife) throw new HealingNotNeeded;
    $item->amount -= 1;
    $life = $item->item->strength;
    if($item->amount < 1) {
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
   * @param int $user
   * @return ItemEntity|NULL
   */
  function getWeapon($user) {
    $weapon = $this->orm->userItems->getWornWeapon($user);
    if($weapon) return $weapon->item;
    else return NULL;
  }
  
  /**
   * @param int $user
   * @return ItemEntity|NULL
   */
  function getArmor($user) {
    $armor = $this->orm->userItems->getWornArmor($user);
    if($armor) return $armor->item;
    else return NULL;
  }
}

class ItemNotOwnedException extends AccessDeniedException {
  
}

class ItemNotEquipableException extends AccessDeniedException {
  
}

class ItemAlreadyWornException extends AccessDeniedException {
  
}

class ItemNotWornException extends AccessDeniedException {
  
}

class ItemNotDrinkableException extends AccessDeniedException {

}

class HealingNotNeeded extends AccessDeniedException {

}
?>