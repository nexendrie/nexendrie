<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Order as OrderEntity,
    Nexendrie\Orm\User as UserEntity;

/**
 * Order Model
 *
 * @author Jakub Konečný
 * @property-read int $foundingPrice
 * @property-read int $maxRank
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
   * Edit specified order
   * 
   * @param int $id
   * @param array $data
   * @return void
   * @throws OrderNotFoundException
   */
  function editOrder($id, array $data) {
    try {
      $order = $this->getOrder($id);
    } catch(OrderNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      $order->$key = $value;
    }
    $this->orm->orders->persistAndFlush($order);
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
    $user->orderRank = $this->maxRank;
    $this->orm->users->persistAndFlush($user);
  }
  
  function calculateOrderIncomeBonus($baseIncome) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $bonus = $increase = 0;
    $user = $this->orm->users->getById($this->user->id);
    if($user->order AND $user->group->path === "tower") {
      $increase += $user->orderRank->adventureBonus + ($user->order->level * 2.5) - 2.5;
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
  
  /**
   * Check whetever the user can leave order
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canLeave( ) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->order) return false;
    else return !($user->orderRank->id === $this->maxRank);
  }
  
  /**
   * Leave order
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotLeaveOrderException
   */
  function leave() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    if(!$this->canLeave()) throw new CannotLeaveOrderException;
    $user = $this->orm->users->getById($this->user->id);
    $user->order = $user->orderRank = NULL;
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Check whetever the user can manage order
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canManage() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->order) return false;
    else return ($user->orderRank->id === $this->maxRank);
  }
  
  /**
   * Check whetever the user can upgrade order
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canUpgrade() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->order) return false;
    elseif($user->orderRank->id != $this->maxRank) return false;
    elseif($user->order->level >= OrderEntity::MAX_LEVEL) return false;
    else return true;
  }
  
  /**
   * Upgrade order
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeOrderException
   * @throws InsufficientFundsException
   */
  function upgrade() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    if(!$this->canUpgrade()) throw new CannotUpgradeOrderException;
    $order = $this->getUserOrder();
    if($order->money < $order->upgradePrice) throw new InsufficientFundsException;
    $order->money -= $order->upgradePrice;
    $order->level++;
    $this->orm->orders->persistAndFlush($order);
  }
  
  /**
   * Get members of specified order
   * 
   * @param int $order
   * @return UserEntity[]
   */
  function getMembers($order) {
    return $this->orm->users->findByOrder($order);
  }
  
  /**
   * @return int
   */
  function getMaxRank() {
    static $rank = NULL;
    if($rank === NULL) $rank = $this->orm->orderRanks->findAll()->countStored();
    return $rank;
  }
  
  /**
   * Promote a user
   * 
   * @param int $userId User's id
   * @return void
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws UserNotFoundException
   * @throws UserNotInYourOrderException
   * @throws CannotPromoteMemberException
   */
  function promote($userId) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canManage()) throw new MissingPermissionsException;
    $user = $this->orm->users->getById($userId);
    if(!$user) throw new UserNotFoundException;
    $admin = $this->orm->users->getById($this->user->id);
    if($user->order->id != $admin->order->id) throw new UserNotInYourOrderException;
    elseif($user->orderRank->id >= $this->maxRank - 1) throw new CannotPromoteMemberException;
    $user->orderRank = $this->orm->orderRanks->getById($user->orderRank->id + 1);
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Demote a user
   * 
   * @param int $userId User's id
   * @return void
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws UserNotFoundException
   * @throws UserNotInYourOrderException
   * @throws CannotDemoteMemberException
   */
  function demote($userId) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canManage()) throw new MissingPermissionsException;
    $user = $this->orm->users->getById($userId);
    if(!$user) throw new UserNotFoundException;
    $admin = $this->orm->users->getById($this->user->id);
    if($user->order->id != $admin->order->id) throw new UserNotInYourOrderException;
    elseif($user->orderRank->id < 2 OR $user->orderRank->id === $this->maxRank) throw new CannotDemoteMemberException;
    $user->orderRank = $this->orm->orderRanks->getById($user->orderRank->id - 1);
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Kick a user
   * 
   * @param int $userId User's id
   * @return void
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws UserNotFoundException
   * @throws UserNotInYourOrderException
   * @throws CannotKickMemberException
   */
  function kick($userId) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canManage()) throw new MissingPermissionsException;
    $user = $this->orm->users->getById($userId);
    if(!$user) throw new UserNotFoundException;
    $admin = $this->orm->users->getById($this->user->id);
    if($user->order->id != $admin->order->id) throw new UserNotInYourOrderException;
    elseif($user->orderRank->id === $this->maxRank) throw new CannotKickMemberException;
    $user->order = $user->orderRank = NULL;
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

class CannotLeaveOrderException extends AccessDeniedException {
  
}

class CannotUpgradeOrderException extends AccessDeniedException {

}

class UserNotInYourOrderException extends AccessDeniedException {
  
}
?>