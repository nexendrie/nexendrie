<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Item as ItemEntity;

/**
 * Equipment Model
 *
 * @author Jakub Konečný
 */
class Equipment extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
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
    elseif($item->item->type === "item") throw new ItemNotEquipableException;
    elseif(!$item->worn) throw new ItemNotWornException;
    $item->worn = false;
    $this->orm->userItems->persistAndFlush($item);
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
?>