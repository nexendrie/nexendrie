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
}

class CannotBuyMoreHousesException extends AccessDeniedException {
  
}

class CannotBuyHouse extends AccessDeniedException {
  
}
?>