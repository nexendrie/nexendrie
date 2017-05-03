<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Monastery as MonasteryEntity,
    Nexendrie\Orm\MonasteryDonation,
    Nexendrie\Orm\Group as GroupEntity,
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
  
  function __construct(Events $eventsModel, Guild $guildModel, Order $orderModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user, SettingsRepository $sr) {
    $this->eventsModel = $eventsModel;
    $this->guildModel = $guildModel;
    $this->orderModel = $orderModel;
    $this->orm = $orm;
    $this->user = $user;
    $this->buildingPrice = $sr->settings["fees"]["buildMonastery"];
  }
  
  /**
   * @return int
   */
  function getBuildingPrice() :int {
    return $this->buildingPrice;
  }
  
  /**
   * Get list of all monasteries
   * 
   * @return MonasteryEntity[]|ICollection
   */
  function listOfMonasteries(): ICollection {
    return $this->orm->monasteries->findAll()
      ->orderBy("level", ICollection::DESC)
      ->orderBy("founded");
  }
  
  /**
   * Get specified monastery
   * 
   * @param int $id
   * @return MonasteryEntity
   * @throws MonasteryNotFoundException
   */
  function get(int $id): MonasteryEntity {
    $monastery = $this->orm->monasteries->getById($id);
    if(!$monastery) {
      throw new MonasteryNotFoundException;
    } else {
      return $monastery;
    }
  }
  
  /**
   * Get specified user's monastary
   * 
   * @param int $id
   * @return MonasteryEntity
   * @throws UserNotFoundException
   * @throws NotInMonasteryException
   */
  function getByUser(int $id = 0): MonasteryEntity {
    if($id === 0) {
      $id = $this->user->id;
    }
    $user = $this->orm->users->getById($id);
    if(!$user) {
      throw new UserNotFoundException;
    } elseif(!$user->monastery) {
      throw new NotInMonasteryException;
    } else {
      return $user->monastery;
    }
  }
  
  /**
   * Check whetever the user can join a monastery
   * 
   * @return bool
   */
  function canJoin(): bool {
    $month = 60 * 60 * 24 * 31;
    if(!$this->user->isLoggedIn()) {
      return false;
    }
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery AND $user->group->path === GroupEntity::PATH_CITY) {
      if($user->guild AND $user->guildRank->id === $this->guildModel->maxRank) {
        return false;
      } else {
        return true;
      }
    } elseif(!$user->monastery AND $user->group->path === GroupEntity::PATH_TOWER) {
      if($user->order AND $user->orderRank->id === $this->orderModel->maxRank) {
        return false;
      } else {
        return true;
      }
    } elseif($user->group->path === GroupEntity::PATH_CHURCH) {
      if($user->monasteriesLed->countStored()) {
        return false;
      } elseif($user->lastTransfer === NULL) {
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
   * @param int $id
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotJoinMonasteryException
   * @throws MonasteryNotFoundException
   * @throws CannotJoinOwnMonasteryException
   */
  function join(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    } elseif(!$this->canJoin()) {
      throw new CannotJoinMonasteryException;
    }
    try {
      $monastery = $this->get($id);
    } catch(MonasteryNotFoundException $e) {
      throw $e;
    }
    $user = $this->orm->users->getById($this->user->id);
    if($user->monastery AND $user->monastery->id === $monastery->id) {
      throw new CannotJoinOwnMonasteryException;
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
   * Check whetever the user can pray (now)
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canPray(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery) {
      return false;
    } elseif($user->monastery->hp <= 30) {
      return false;
    } elseif($user->life >= $user->maxLife) {
      return false;
    } elseif(!$user->lastPrayer) {
      return true;
    }
    $oneDay = 60 * 60 * 24;
    if($user->lastPrayer + $oneDay < time()) {
      return true;
    } else {
      return false;
    }
  }
  
  /**
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotPrayException
   */
  function pray(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    if(!$this->canPray()) {
      throw new CannotPrayException;
    }
    $user = $this->orm->users->getById($this->user->id);
    $user->lastPrayer = time();
    $user->life += $this->prayerLife();
    $user->prayers++;
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Check whetever the user can leave monastery
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canLeave(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery) {
      return false;
    } elseif($user->id === $user->monastery->leader->id) {
      return false;
    } else {
      return true;
    }
  }
  
  /**
   * Leave monastery
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotLeaveMonasteryException
   */
  function leave(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    if(!$this->canLeave()) {
      throw new CannotLeaveMonasteryException;
    }
    $user = $this->orm->users->getById($this->user->id);
    $user->monastery = NULL;
    if($user->ownedTowns->countStored() OR $this->orm->castles->getByOwner($this->user->id)) {
      $user->group = $this->orm->groups->getByLevel(400);
    } else {
      $user->group = $this->orm->groups->getByLevel(50);
    }
    $this->orm->users->persistAndFlush($user);
    $this->user->identity->group = $user->group->id;
    $this->user->identity->level = $user->group->level;
    $this->user->identity->roles = [$user->group->singleName];
    $this->user->identity->path = $user->group->path;
  }
  
  /**
   * Check whetever the user can build monastery
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canBuild(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    if($this->user->identity->level != 550) {
      return false;
    }
    $user = $this->orm->users->getById($this->user->id);
    if($user->monasteriesLed->countStored() > 0) {
      return false;
    } else {
      return true;
    }
  }
  
  /**
   * Build a monastery
   * 
   * @param string $name
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotBuildMonasteryException
   * @throws MonasteryNameInUseException
   * @throws InsufficientFundsException
   */
  function build(string $name): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    if(!$this->canBuild()) {
      throw new CannotBuildMonasteryException;
    }
    if($this->orm->monasteries->getByName($name)) {
      throw new MonasteryNameInUseException;
    }
    $user = $this->orm->users->getById($this->user->id);
    if($user->money < $this->buildingPrice) {
      throw new InsufficientFundsException;
    }
    $monastery = new MonasteryEntity;
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
   * @param int $amount
   * @return void
   * @throws AuthenticationNeededException
   * @throws NotInMonasteryException
   * @throws InsufficientFundsException
   */
  function donate(int $amount): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery) {
      throw new NotInMonasteryException;
    } elseif($user->money < $amount) {
      throw new InsufficientFundsException;
    }
    $user->money -= $amount;
    $user->monastery->money += $amount;
    $donation = new MonasteryDonation;
    $donation->user = $user;
    $donation->monastery = $user->monastery;
    $donation->amount = $amount;
    $this->orm->monasteryDonations->persistAndFlush($donation);
  }
  
  /**
   * Edit specified monastery
   * 
   * @param int $id
   * @param array $data
   * @return void
   * @throws MonasteryNotFoundException
   * @throws MonasteryNameInUseException
   */
  function edit(int $id, array $data): void {
    try {
      $monastery = $this->get($id);
    } catch(MonasteryNotFoundException $e) {
      throw $e;
    }
    $skip = ["town", "founded", "money"];
    foreach($data as $key => $value) {
      if($key === "name") {
        $m = $this->orm->monasteries->getByName($value);
        if($m AND $m->id != $id) {
          throw new MonasteryNameInUseException;
        }
        $monastery->$key = $value;
      } elseif(in_array($key, $skip)) {
        continue;
      } else {
        $monastery->$key = $value;
      }
    }
    $this->orm->monasteries->persistAndFlush($monastery);
  }
  
  /**
   * Get high clerics of a monastery
   * 
   * @param int $id Monastery' id
   * @return string[] id => publicname
   */
  function highClerics(int $id): array {
    return $this->orm->users->findByMonastery($id)
      ->findBy(["this->group->level" => 550])
      ->fetchPairs("id", "publicname");
  }
  
  /**
   * Check whetever the user can manage monastery
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canManage(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery) {
      return false;
    } elseif($user->monastery->leader->id != $this->user->id) {
      return false;
    } else {
      return true;
    }
  }
  
  /**
   * How many hitpoints will the prayer add
   * 
   * @return int
   */
  function prayerLife(): int {
    if(!$this->user->isLoggedIn()) {
      return 0;
    }
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery) {
      return 0;
    }
    $baseValue = $user->monastery->prayerLife;
    return $baseValue + $this->eventsModel->calculatePrayerLifeBonus($baseValue);
  }
  
  /**
   * Check whetever the user can upgrade monastery
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canUpgrade(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery) {
      return false;
    } elseif($user->monastery->leader->id != $this->user->id) {
      return false;
    } elseif($user->monastery->level >= MonasteryEntity::MAX_LEVEL) {
      return false;
    } else {
      return true;
    }
  }
  
  /**
   * Upgrade monastery
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeMonasteryException
   * @throws InsufficientFundsException
   */
  function upgrade(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    if(!$this->canUpgrade()) {
      throw new CannotUpgradeMonasteryException;
    }
    $user = $this->orm->users->getById($this->user->id);
    $upgradePrice = $user->monastery->upgradePrice;
    if($user->monastery->money < $upgradePrice) {
      throw new InsufficientFundsException;
    }
    $user->monastery->money -= $upgradePrice;
    $user->monastery->level++;
    $this->orm->monasteries->persistAndFlush($user->monastery);
  }
  
  /**
   * Check whetever the user can repair monastery
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canRepair(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery) {
      return false;
    } elseif($user->monastery->leader->id != $this->user->id) {
      return false;
    } elseif($user->monastery->hp >= 100) {
      return false;
    } else {
      return true;
    }
  }
  
  /**
   * Repair monastery
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotRepairMonasteryException
   * @throws InsufficientFundsException
   */
  function repair(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    if(!$this->canRepair()) {
      throw new CannotRepairMonasteryException;
    }
    $user = $this->orm->users->getById($this->user->id);
    $repairPrice = $user->monastery->repairPrice;
    if($user->monastery->money < $repairPrice) {
      throw new InsufficientFundsException;
    }
    $user->monastery->hp = 100;
    $user->monastery->money -= $repairPrice;
    $this->orm->monasteries->persistAndFlush($user->monastery);
  }
}
?>