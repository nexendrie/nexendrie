<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\Order as OrderEntity;
use Nexendrie\Orm\User as UserEntity;
use Nexendrie\Orm\Group as GroupEntity;
use Nextras\Orm\Collection\ICollection;

/**
 * Order Model
 *
 * @author Jakub Konečný
 * @property-read int $maxRank
 */
final class Order {
  private int $foundingPrice;
  
  use \Nette\SmartObject;
  
  public function __construct(private readonly ORM $orm, private readonly \Nette\Security\User $user, SettingsRepository $sr) {
    $this->foundingPrice = $sr->settings["fees"]["foundOrder"];
  }
  
  /**
   * Get list of orders
   * 
   * @return OrderEntity[]|ICollection
   */
  public function listOfOrders(): ICollection {
    return $this->orm->orders->findAll();
  }
  
  /**
   * Get specified order
   *
   * @throws OrderNotFoundException
   */
  public function getOrder(int $id): OrderEntity {
    $order = $this->orm->orders->getById($id);
    return $order ?? throw new OrderNotFoundException();
  }
  
  /**
   * Check whether a name can be used
   */
  private function checkNameAvailability(string $name, int $id = null): bool {
    $order = $this->orm->orders->getByName($name);
    return $order === null || $order->id === $id;
  }
  
  /**
   * Edit specified order
   *
   * @throws OrderNotFoundException
   * @throws OrderNameInUseException
   */
  public function editOrder(int $id, array $data): void {
    try {
      $order = $this->getOrder($id);
    } catch(OrderNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      if($key === "name" && !$this->checkNameAvailability($value, $id)) {
        throw new OrderNameInUseException();
      }
      $order->$key = $value;
    }
    $this->orm->orders->persistAndFlush($order);
  }
  
  /**
   * Get specified user's order
   */
  public function getUserOrder(int $uid = null): ?OrderEntity {
    $user = $this->orm->users->getById($uid ?? $this->user->id);
    return $user?->order;
  }
  
  /**
   * Check whether the user can found an order
   */
  public function canFound(): bool {
    if(!$this->user->isLoggedIn()) {
      return false;
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path !== GroupEntity::PATH_TOWER) {
      return false;
    }
    $ranks = $this->orm->groups->getTowerGroupIds();
    end($ranks);
    if($user->group->id > current($ranks)) {
      return false;
    } elseif($user->order !== null) {
      return false;
    }
    return true;
  }
  
  /**
   * Found an order
   *
   * @throws CannotFoundOrderException
   * @throws OrderNameInUseException
   * @throws InsufficientFundsException
   */
  public function found(array $data): void {
    if(!$this->canFound()) {
      throw new CannotFoundOrderException();
    }
    if(!$this->checkNameAvailability($data["name"])) {
      throw new OrderNameInUseException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->money < $this->foundingPrice) {
      throw new InsufficientFundsException();
    }
    $order = new OrderEntity();
    $this->orm->orders->attach($order);
    $order->name = $data["name"];
    $order->description = $data["description"];
    $user->lastActive = time();
    $user->money -= $this->foundingPrice;
    $user->order = $order;
    $user->orderRank = $this->maxRank;
    $this->orm->users->persist($user);
    /** @var UserEntity $queen */
    $queen = $this->orm->users->getById(0);
    $queen->money += $this->foundingPrice;
    $this->orm->users->persist($queen);
    $this->orm->flush();
  }
  
  public function calculateOrderIncomeBonus(int $baseIncome): int {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    return (int) ($baseIncome / 100 * $user->adventureBonusIncome);
  }
  
  /**
   * Check whether the user can join an order
   */
  public function canJoin(): bool {
    if(!$this->user->isLoggedIn()) {
      return false;
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    return ($user->group->path === GroupEntity::PATH_TOWER && $user->order === null);
  }
  
  /**
   * Join an order
   *
   * @throws AuthenticationNeededException
   * @throws CannotJoinOrderException
   * @throws OrderNotFoundException
   */
  public function join(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif(!$this->canJoin()) {
      throw new CannotJoinOrderException();
    }
    try {
      $order = $this->getOrder($id);
    } catch(OrderNotFoundException $e) {
      throw $e;
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    $user->order = $order;
    $user->orderRank = 1;
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Check whether the user can leave order
   *
   * @throws AuthenticationNeededException
   */
  public function canLeave(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->order === null || $user->orderRank === null) {
      return false;
    }
    return !($user->orderRank->id === $this->maxRank);
  }
  
  /**
   * Leave order
   *
   * @throws AuthenticationNeededException
   * @throws CannotLeaveOrderException
   */
  public function leave(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    if(!$this->canLeave()) {
      throw new CannotLeaveOrderException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    $user->order = $user->orderRank = null;
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Check whether the user can manage order
   *
   * @throws AuthenticationNeededException
   */
  public function canManage(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    return $this->user->isAllowed(AuthorizatorFactory::ORDER_RESOURCE_NAME, "manage");
  }
  
  /**
   * Check whether the user can upgrade order
   *
   * @throws AuthenticationNeededException
   */
  public function canUpgrade(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->order === null) {
      return false;
    } elseif(!$this->user->isAllowed(AuthorizatorFactory::ORDER_RESOURCE_NAME, "upgrade")) {
      return false;
    } elseif($user->order->level >= OrderEntity::MAX_LEVEL) {
      return false;
    }
    return true;
  }
  
  /**
   * Upgrade order
   *
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeOrderException
   * @throws InsufficientFundsException
   */
  public function upgrade(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    if(!$this->canUpgrade()) {
      throw new CannotUpgradeOrderException();
    }
    /** @var OrderEntity $order */
    $order = $this->getUserOrder();
    if($order->money < $order->upgradePrice) {
      throw new InsufficientFundsException();
    }
    $order->money -= $order->upgradePrice;
    $order->level++;
    $this->orm->orders->persistAndFlush($order);
  }
  
  /**
   * Get members of specified order
   *
   * @return UserEntity[]|ICollection
   */
  public function getMembers(int $order): ICollection {
    return $this->orm->users->findByOrder($order);
  }
  
  protected function getMaxRank(): int {
    static $rank = null;
    if($rank === null) {
      $rank = $this->orm->orderRanks->findAll()->countStored();
    }
    return $rank;
  }
  
  /**
   * Promote a user
   *
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws UserNotFoundException
   * @throws UserNotInYourOrderException
   * @throws CannotPromoteMemberException
   */
  public function promote(int $userId): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif(!$this->user->isAllowed(AuthorizatorFactory::ORDER_RESOURCE_NAME, "promote")) {
      throw new MissingPermissionsException();
    }
    $user = $this->orm->users->getById($userId);
    if($user === null) {
      throw new UserNotFoundException();
    }
    /** @var UserEntity $admin */
    $admin = $this->orm->users->getById($this->user->id);
    if($admin->order === null || $user->order === null || $user->order->id !== $admin->order->id || $user->orderRank === null) {
      throw new UserNotInYourOrderException();
    } elseif($user->orderRank->id >= $this->maxRank - 1) {
      throw new CannotPromoteMemberException();
    }
    $user->orderRank = $this->orm->orderRanks->getById($user->orderRank->id + 1);
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Demote a user
   *
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws UserNotFoundException
   * @throws UserNotInYourOrderException
   * @throws CannotDemoteMemberException
   */
  public function demote(int $userId): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif(!$this->user->isAllowed(AuthorizatorFactory::ORDER_RESOURCE_NAME, "demote")) {
      throw new MissingPermissionsException();
    }
    $user = $this->orm->users->getById($userId);
    if($user === null) {
      throw new UserNotFoundException();
    }
    /** @var UserEntity $admin */
    $admin = $this->orm->users->getById($this->user->id);
    if($admin->order === null || $user->order === null || $user->order->id !== $admin->order->id || $user->orderRank === null) {
      throw new UserNotInYourOrderException();
    } elseif($user->orderRank->id < 2 || $user->orderRank->id === $this->maxRank) {
      throw new CannotDemoteMemberException();
    }
    $user->orderRank = $this->orm->orderRanks->getById($user->orderRank->id - 1);
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Kick a user
   *
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws UserNotFoundException
   * @throws UserNotInYourOrderException
   * @throws CannotKickMemberException
   */
  public function kick(int $userId): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif(!$this->user->isAllowed(AuthorizatorFactory::ORDER_RESOURCE_NAME, "kick")) {
      throw new MissingPermissionsException();
    }
    $user = $this->orm->users->getById($userId);
    if($user === null) {
      throw new UserNotFoundException();
    }
    /** @var UserEntity $admin */
    $admin = $this->orm->users->getById($this->user->id);
    if($admin->order === null || $user->order === null || $user->order->id !== $admin->order->id || $user->orderRank === null) {
      throw new UserNotInYourOrderException();
    } elseif($user->orderRank->id === $this->maxRank) {
      throw new CannotKickMemberException();
    }
    $user->order = $user->orderRank = null;
    $this->orm->users->persistAndFlush($user);
  }
}
?>