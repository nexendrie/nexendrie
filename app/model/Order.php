<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Order as OrderEntity;

/**
 * Order Model
 *
 * @author Jakub Konečný
 * @property-read int $foundingPrice
 */
class Order extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  protected $foundingPrice;
  
  function __construct($foundingPrice, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
    $this->foundingPrice = $foundingPrice;
  }
  
  /**
   * @return int
   */
  function getFoundingPrice() {
    return $this->foundingPrice;
  }
  
  /**
   * Get list of orders
   * 
   * @return OrderEntity[]
   */
  function listOfOrders() {
    return $this->orm->orders->findAll();
  }
  
  /**
   * Get specified order
   * 
   * @param int $id
   * @return OrderEntity
   * @throws OrderNotFoundException
   */
  function getOrder($id) {
    $order = $this->orm->orders->getById($id);
    if(!$order) throw new OrderNotFoundException;
    else return $order;
  }
  
  /**
   * Get specified user's order
   * 
   * @param int $uid
   * @return OrderEntity|NULL
   */
  function getUserOrder($uid = 0) {
    if($uid === 0) $uid = $this->user->id;
    $user = $this->orm->users->getById($uid);
    return $user->order;
  }
  
  /**
   * Check whetever the user can found an order
   * 
   * @return bool
   */
  function canFound() {
    if(!$this->user->isLoggedIn()) return false;
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path != "tower") return false;
    elseif($user->group->level < 6000) return false;
    elseif($user->order) return false;
    else return true;
  }
  
  /**
   * Found an order
   * 
   * @param array $data
   * @return void
   * @throws CannotFoundOrderException
   * @throws OrderNameInUseException
   * @throws InsufficientFundsException
   */
  function found(array $data) {
    if(!$this->canFound()) throw new CannotFoundOrderException;
    $user = $this->orm->users->getById($this->user->id);
    if($this->orm->guilds->getByName($data["name"])) throw new OrderNameInUseException;
    if($user->money < $this->foundingPrice) throw new InsufficientFundsException;
    $order = new OrderEntity;
    $this->orm->orders->attach($order);
    $order->name = $data["name"];
    $order->description = $data["description"];
    $user->lastActive = $order->founded = time();
    $user->money -= $this->foundingPrice;
    $order->money = $this->foundingPrice;
    $user->order = $order;
    $user->orderRank = 4;
    $this->orm->users->persistAndFlush($user);
  }
  
  function calculateOrderIncomeBonus($baseIncome) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $bonus = $increase = 0;
    $user = $this->orm->users->getById($this->user->id);
    if($user->order AND $user->group->path === "tower") {
      $increase += $user->orderRank->adventureBonus + $user->order->level - 1;
    }
    $bonus += (int) $baseIncome /100 * $increase;
    return $bonus;
  }
  
  /**
   * Check whetever the user can join an order
   * 
   * @return bool
   */
  function canJoin() {
    if(!$this->user->isLoggedIn()) return false;
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path === "tower" AND !$user->order) return true;
    else return false;
  }
  
  /**
   * Join an order
   * 
   * @param int $id
   * @throws AuthenticationNeededException
   * @throws CannotJoinOrderException
   * @throws OrderNotFoundException
   */
  function join($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canJoin()) throw new CannotJoinOrderException;
    try {
      $order = $this->getOrder($id);
    } catch(OrderNotFoundException $e) {
      throw $e;
    }
    $user = $this->orm->users->getById($this->user->id);
    $user->order = $order;
    $user->orderRank = 1;
    $this->orm->users->persistAndFlush($user);
  }
  
}

class OrderNotFoundException extends RecordNotFoundException {
  
}

class CannotFoundOrderException extends AccessDeniedException {
  
}

class OrderNameInUseException extends NameInUseException {
  
}

class CannotJoinOrderException extends AccessDeniedException {
  
}

?>