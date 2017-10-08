<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Monastery as MonasteryEntity,
    Nexendrie\Orm\MonasteryDonation,
    Nexendrie\Orm\Group as GroupEntity,
    Nexendrie\Orm\User as UserEntity,
    Nextras\Orm\Collection\ICollection;

/**
 * Monastery Model
 *
 * @author Jakub Konečný
 * @property-read int $buildingPrice
 * 
 */
class Monastery {
  /** @var Events */
  protected $eventsModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var Guild */
  protected $guildModel;
  /** @var Order */
  protected $orderModel;
  /** @var int */
  protected $buildingPrice;
  
  use \Nette\SmartObject;
  
  public function __construct(Events $eventsModel, Guild $guildModel, Order $orderModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user, SettingsRepository $sr) {
    $this->eventsModel = $eventsModel;
    $this->guildModel = $guildModel;
    $this->orderModel = $orderModel;
    $this->orm = $orm;
    $this->user = $user;
    $this->buildingPrice = $sr->settings["fees"]["buildMonastery"];
  }
  
  public function getBuildingPrice() :int {
    return $this->buildingPrice;
  }
  
  /**
   * Get list of all monasteries
   * 
   * @return MonasteryEntity[]|ICollection
   */
  public function listOfMonasteries(): ICollection {
    return $this->orm->monasteries->findAll()
      ->orderBy("level", ICollection::DESC)
      ->orderBy("founded");
  }
  
  /**
   * Get specified monastery
   *
   * @throws MonasteryNotFoundException
   */
  public function get(int $id): MonasteryEntity {
    $monastery = $this->orm->monasteries->getById($id);
    if(is_null($monastery)) {
      throw new MonasteryNotFoundException();
    }
    return $monastery;
  }
  
  /**
   * Get specified user's monastery
   *
   * @throws UserNotFoundException
   * @throws NotInMonasteryException
   */
  public function getByUser(int $id = NULL): MonasteryEntity {
    $user = $this->orm->users->getById($id ?? $this->user->id);
    if(is_null($user)) {
      throw new UserNotFoundException();
    } elseif(!$user->monastery) {
      throw new NotInMonasteryException();
    }
    return $user->monastery;
  }
  
  /**
   * Check whether the user can join a monastery
   */
  public function canJoin(): bool {
    $month = 60 * 60 * 24 * 31;
    if(!$this->user->isLoggedIn()) {
      return false;
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery AND $user->group->path === GroupEntity::PATH_CITY) {
      return !($user->guild AND $user->guildRank->id === $this->guildModel->maxRank);
    } elseif(!$user->monastery AND $user->group->path === GroupEntity::PATH_TOWER) {
      return !($user->order AND $user->orderRank->id === $this->orderModel->maxRank);
    } elseif($user->group->path === GroupEntity::PATH_CHURCH) {
      if($user->monasteriesLed->countStored()) {
        return false;
      } elseif(is_null($user->lastTransfer)) {
        return true;
      } elseif($user->lastTransfer  + $month < time()) {
        return true;
      }
    }
    return false;
  }
  
  /**
   * Join a monastery
   *
   * @throws AuthenticationNeededException
   * @throws CannotJoinMonasteryException
   * @throws MonasteryNotFoundException
   * @throws CannotJoinOwnMonasteryException
   */
  public function join(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif(!$this->canJoin()) {
      throw new CannotJoinMonasteryException();
    }
    try {
      $monastery = $this->get($id);
    } catch(MonasteryNotFoundException $e) {
      throw $e;
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->monastery AND $user->monastery->id === $monastery->id) {
      throw new CannotJoinOwnMonasteryException();
    }
    $user->lastTransfer = $user->lastActive = time();
    $user->monastery = $monastery;
    if($user->group->path != GroupEntity::PATH_CHURCH) {
      $user->group = $this->orm->groups->getByLevel(55);
    }
    $user->town = $monastery->town;
    $user->guild = $user->guildRank = NULL;
    $user->order = $user->orderRank = NULL;
    $this->orm->users->persistAndFlush($user);
    $this->user->identity->group = $user->group->id;
    $this->user->identity->level = $user->group->level;
    $this->user->identity->town = $user->town->id;
    $this->user->identity->roles = [$user->group->singleName];
    $this->user->identity->path = $user->group->path;
  }
  
  /**
   * Check whether the user can pray (now)
   *
   * @throws AuthenticationNeededException
   */
  public function canPray(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if(is_null($user->monastery)) {
      return false;
    } elseif($user->monastery->hp <= 30) {
      return false;
    } elseif($user->life >= $user->maxLife) {
      return false;
    } elseif(is_null($user->lastPrayer)) {
      return true;
    }
    $oneDay = 60 * 60 * 24;
    return ($user->lastPrayer + $oneDay < time());
  }
  
  /**
   * @throws AuthenticationNeededException
   * @throws CannotPrayException
   */
  public function pray(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    if(!$this->canPray()) {
      throw new CannotPrayException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    $user->lastPrayer = time();
    $user->life += $this->prayerLife();
    $user->prayers++;
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Check whether the user can leave monastery
   *
   * @throws AuthenticationNeededException
   */
  public function canLeave(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if(is_null($user->monastery)) {
      return false;
    } elseif($user->id === $user->monastery->leader->id) {
      return false;
    }
    return true;
  }
  
  /**
   * Leave monastery
   *
   * @throws AuthenticationNeededException
   * @throws CannotLeaveMonasteryException
   */
  public function leave(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    if(!$this->canLeave()) {
      throw new CannotLeaveMonasteryException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    $user->monastery = NULL;
    $level = 50;
    if($user->ownedTowns->countStored() OR $this->orm->castles->getByOwner($this->user->id)) {
      $level = 400;
    }
    /** @var GroupEntity $group */
    $group = $this->orm->groups->getByLevel($level);
    $user->group = $group;
    $this->orm->users->persistAndFlush($user);
    $this->user->identity->group = $user->group->id;
    $this->user->identity->level = $user->group->level;
    $this->user->identity->roles = [$user->group->singleName];
    $this->user->identity->path = $user->group->path;
  }
  
  /**
   * Check whether the user can build monastery
   *
   * @throws AuthenticationNeededException
   */
  public function canBuild(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    if($this->user->identity->level != 550) {
      return false;
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    return !($user->monasteriesLed->countStored() > 0);
  }
  
  /**
   * Build a monastery
   *
   * @throws AuthenticationNeededException
   * @throws CannotBuildMonasteryException
   * @throws MonasteryNameInUseException
   * @throws InsufficientFundsException
   */
  public function build(string $name): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    if(!$this->canBuild()) {
      throw new CannotBuildMonasteryException();
    }
    if($this->orm->monasteries->getByName($name)) {
      throw new MonasteryNameInUseException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->money < $this->buildingPrice) {
      throw new InsufficientFundsException();
    }
    $monastery = new MonasteryEntity();
    $this->orm->monasteries->attach($monastery);
    $monastery->name = $name;
    $monastery->leader = $user;
    $monastery->town = $this->user->identity->town;
    $monastery->leader->lastActive = time();
    $user->money -= $this->buildingPrice;
    $this->orm->monasteries->persistAndFlush($monastery);
    $user->monastery = $this->orm->monasteries->getByName($name);
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Donate money to monastery
   *
   * @throws AuthenticationNeededException
   * @throws NotInMonasteryException
   * @throws InsufficientFundsException
   */
  public function donate(int $amount): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if(is_null($user->monastery)) {
      throw new NotInMonasteryException();
    } elseif($user->money < $amount) {
      throw new InsufficientFundsException();
    }
    $user->money -= $amount;
    $user->monastery->money += $amount;
    $donation = new MonasteryDonation();
    $donation->user = $user;
    $donation->monastery = $user->monastery;
    $donation->amount = $amount;
    $this->orm->monasteryDonations->persistAndFlush($donation);
  }
  
  /**
   * Edit specified monastery
   *
   * @throws MonasteryNotFoundException
   * @throws MonasteryNameInUseException
   */
  public function edit(int $id, array $data): void {
    try {
      $monastery = $this->get($id);
    } catch(MonasteryNotFoundException $e) {
      throw $e;
    }
    $skip = ["town", "founded", "money"];
    foreach($data as $key => $value) {
      if(in_array($key, $skip)) {
        continue;
      }
      if($key === "name") {
        $m = $this->orm->monasteries->getByName($value);
        if($m AND $m->id != $id) {
          throw new MonasteryNameInUseException();
        }
      }
      $monastery->$key = $value;
    }
    $this->orm->monasteries->persistAndFlush($monastery);
  }
  
  /**
   * Get high clerics of a monastery
   *
   * @return string[] id => publicname
   */
  public function highClerics(int $id): array {
    return $this->orm->users->findByMonastery($id)
      ->findBy(["this->group->level" => 550])
      ->fetchPairs("id", "publicname");
  }
  
  /**
   * Check whether the user can manage monastery
   *
   * @throws AuthenticationNeededException
   */
  public function canManage(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if(is_null($user->monastery)) {
      return false;
    } elseif($user->monastery->leader->id != $this->user->id) {
      return false;
    }
    return true;
  }
  
  /**
   * How many hitpoints will the prayer add
   */
  public function prayerLife(): int {
    if(!$this->user->isLoggedIn()) {
      return 0;
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if(is_null($user->monastery)) {
      return 0;
    }
    $baseValue = $user->monastery->prayerLife;
    return $baseValue + $this->eventsModel->calculatePrayerLifeBonus($baseValue);
  }
  
  /**
   * Check whether the user can upgrade monastery
   *
   * @throws AuthenticationNeededException
   */
  public function canUpgrade(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if(is_null($user->monastery)) {
      return false;
    } elseif($user->monastery->leader->id != $this->user->id) {
      return false;
    } elseif($user->monastery->level >= MonasteryEntity::MAX_LEVEL) {
      return false;
    }
    return true;
  }
  
  /**
   * Upgrade monastery
   *
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeMonasteryException
   * @throws InsufficientFundsException
   */
  public function upgrade(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    if(!$this->canUpgrade()) {
      throw new CannotUpgradeMonasteryException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    $upgradePrice = $user->monastery->upgradePrice;
    if($user->monastery->money < $upgradePrice) {
      throw new InsufficientFundsException();
    }
    $user->monastery->money -= $upgradePrice;
    $user->monastery->level++;
    $this->orm->monasteries->persistAndFlush($user->monastery);
  }
  
  /**
   * Check whether the user can repair monastery
   *
   * @throws AuthenticationNeededException
   */
  public function canRepair(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if(is_null($user->monastery)) {
      return false;
    } elseif($user->monastery->leader->id != $this->user->id) {
      return false;
    } elseif($user->monastery->hp >= 100) {
      return false;
    }
    return true;
  }
  
  /**
   * Repair monastery
   *
   * @throws AuthenticationNeededException
   * @throws CannotRepairMonasteryException
   * @throws InsufficientFundsException
   */
  public function repair(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    if(!$this->canRepair()) {
      throw new CannotRepairMonasteryException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    $repairPrice = $user->monastery->repairPrice;
    if($user->monastery->money < $repairPrice) {
      throw new InsufficientFundsException();
    }
    $user->monastery->hp = 100;
    $user->monastery->money -= $repairPrice;
    $this->orm->monasteries->persistAndFlush($user->monastery);
  }
}
?>