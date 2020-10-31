<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Monastery as MonasteryEntity;
use Nexendrie\Orm\MonasteryDonation;
use Nexendrie\Orm\Group as GroupEntity;
use Nexendrie\Orm\User as UserEntity;
use Nextras\Orm\Collection\ICollection;

/**
 * Monastery Model
 *
 * @author Jakub Konečný
 * 
 */
final class Monastery {
  protected Events $eventsModel;
  protected \Nexendrie\Orm\Model $orm;
  protected \Nette\Security\User $user;
  protected Guild $guildModel;
  protected Order $orderModel;
  protected int $buildingPrice;
  protected int $criticalCondition;
  
  use \Nette\SmartObject;
  
  public function __construct(Events $eventsModel, Guild $guildModel, Order $orderModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user, SettingsRepository $sr) {
    $this->eventsModel = $eventsModel;
    $this->guildModel = $guildModel;
    $this->orderModel = $orderModel;
    $this->orm = $orm;
    $this->user = $user;
    $this->buildingPrice = $sr->settings["fees"]["buildMonastery"];
    $this->criticalCondition = $sr->settings["buildings"]["criticalCondition"];
  }
  
  /**
   * Get list of all monasteries
   * 
   * @return MonasteryEntity[]|ICollection
   */
  public function listOfMonasteries(): ICollection {
    return $this->orm->monasteries->findAll()
      ->orderBy("altairLevel", ICollection::DESC)
      ->orderBy("created");
  }
  
  /**
   * Get specified monastery
   *
   * @throws MonasteryNotFoundException
   */
  public function get(int $id): MonasteryEntity {
    $monastery = $this->orm->monasteries->getById($id);
    if($monastery === null) {
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
  public function getByUser(int $id = null): MonasteryEntity {
    $user = $this->orm->users->getById($id ?? $this->user->id);
    if($user === null) {
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
    if(!$user->monastery && $user->group->path === GroupEntity::PATH_CITY) {
      return !($user->guild && $user->guildRank->id === $this->guildModel->maxRank);
    } elseif(!$user->monastery && $user->group->path === GroupEntity::PATH_TOWER) {
      return !($user->order && $user->orderRank->id === $this->orderModel->maxRank);
    } elseif($user->group->path === GroupEntity::PATH_CHURCH) {
      if($user->monasteriesLed->countStored()) {
        return false;
      } elseif($user->lastTransfer === null) {
        return true;
      } elseif($user->lastTransfer + $month < time()) {
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
    if($user->monastery && $user->monastery->id === $monastery->id) {
      throw new CannotJoinOwnMonasteryException();
    }
    $user->lastTransfer = $user->lastActive = time();
    $user->monastery = $monastery;
    if($user->group->path !== GroupEntity::PATH_CHURCH) {
      $ranks = $this->getChurchGroupIds();
      end($ranks);
      $user->group = current($ranks);
    }
    $user->town = $monastery->town;
    $user->guild = $user->guildRank = null;
    $user->order = $user->orderRank = null;
    $this->orm->users->persistAndFlush($user);
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
    if($user->monastery === null) {
      return false;
    } elseif($user->monastery->hp < $this->criticalCondition) {
      return false;
    } elseif($user->life >= $user->maxLife) {
      return false;
    } elseif($user->lastPrayer === null) {
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
    if($user->monastery === null) {
      return false;
    }
    return !($user->id === $user->monastery->leader->id);
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
    $user->monastery = null;

    if($user->ownedTowns->countStored() > 0 || $this->orm->castles->getByOwner($this->user->id) !== null) {
      $ranks = $this->orm->groups->getTowerGroupIds();
    } else {
      $ranks = $this->orm->groups->getCityGroupIds();
    }
    end($ranks);
    $user->group = current($ranks);
    $this->orm->users->persistAndFlush($user);
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
    if($this->user->identity->group !== $this->getChurchGroupIds()[0]) {
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
    if($this->orm->monasteries->getByName($name) !== null) {
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
    $monastery->leader->money -= $this->buildingPrice;
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
    if($user->monastery === null) {
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
    $skip = ["town", "created", "money"];
    foreach($data as $key => $value) {
      if(in_array($key, $skip, true)) {
        continue;
      }
      if($key === "name") {
        $m = $this->orm->monasteries->getByName($value);
        if($m !== null && $m->id !== $id) {
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
      ->findBy(["this->group->id" => $this->getChurchGroupIds()[0]])
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
    if($user->monastery === null) {
      return false;
    }
    return ($user->monastery->leader->id === $this->user->id);
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
    if($user->monastery === null) {
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
    if($user->monastery === null) {
      return false;
    } elseif($user->monastery->leader->id !== $this->user->id) {
      return false;
    }
    return ($user->monastery->altairLevel < MonasteryEntity::MAX_LEVEL);
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
    $user->monastery->altairLevel++;
    $this->orm->monasteries->persistAndFlush($user->monastery);
  }

  /**
   * Check whether the user can upgrade monastery's library
   *
   * @throws AuthenticationNeededException
   */
  public function canUpgradeLibrary(): bool {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->monastery === null) {
      return false;
    } elseif($user->monastery->leader->id !== $this->user->id) {
      return false;
    }
    return ($user->monastery->libraryLevel < MonasteryEntity::MAX_LEVEL - 1);
  }

  /**
   * Upgrade monastery's library
   *
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeMonasteryException
   * @throws InsufficientFundsException
   */
  public function upgradeLibrary(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    if(!$this->canUpgradeLibrary()) {
      throw new CannotUpgradeMonasteryException();
    }
    /** @var UserEntity $user */
    $user = $this->orm->users->getById($this->user->id);
    $upgradePrice = $user->monastery->libraryUpgradePrice;
    if($user->monastery->money < $upgradePrice) {
      throw new InsufficientFundsException();
    }
    $user->monastery->money -= $upgradePrice;
    $user->monastery->libraryLevel++;
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
    if($user->monastery === null) {
      return false;
    } elseif($user->monastery->leader->id !== $this->user->id) {
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

  /**
   * @return int[]
   */
  public function getChurchGroupIds(): array {
    return $this->orm->groups->getChurchGroupIds();
  }

  /**
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws UserNotFoundException
   * @throws UserNotInYourMonasteryException
   * @throws CannotPromoteMemberException
   */
  public function promote(int $userId): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $ranks = $this->getChurchGroupIds();
    /** @var UserEntity $admin */
    $admin = $this->orm->users->getById($this->user->id);
    if($admin->group->id !== $ranks[0]) {
      throw new MissingPermissionsException();
    }
    $user = $this->orm->users->getById($userId);
    if($user === null) {
      throw new UserNotFoundException();
    }
    if($user->monastery === null || $user->monastery->id !== $admin->monastery->id || $user->monastery->leader->id !== $this->user->id) {
      throw new UserNotInYourMonasteryException();
    }
    if($user->group->id <= $ranks[0]) {
      throw new CannotPromoteMemberException();
    }
    $currentRank = $user->group->id;
    $newRank = $ranks[0];
    foreach($ranks as $i => $rank) {
      if($rank === $currentRank) {
        $newRank = $i - 1;
        break;
      }
    }
    $user->group = $ranks[$newRank];
    $this->orm->users->persistAndFlush($user);
  }

  /**
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws UserNotFoundException
   * @throws UserNotInYourMonasteryException
   * @throws CannotPromoteMemberException
   */
  public function demote(int $userId): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $ranks = $this->getChurchGroupIds();
    /** @var UserEntity $admin */
    $admin = $this->orm->users->getById($this->user->id);
    if($admin->group->id !== $ranks[0]) {
      throw new MissingPermissionsException();
    }
    $user = $this->orm->users->getById($userId);
    if($user === null) {
      throw new UserNotFoundException();
    }
    if($user->monastery === null || $user->monastery->id !== $admin->monastery->id || $user->monastery->leader->id !== $this->user->id) {
      throw new UserNotInYourMonasteryException();
    }
    end($ranks);
    if($user->group->id === current($ranks)) {
      throw new CannotDemoteMemberException();
    }
    $ranks = array_reverse($ranks);
    $currentRank = $user->group->id;
    $newRank = $ranks[0];
    foreach($ranks as $i => $rank) {
      if($rank === $currentRank) {
        $newRank = $i - 1;
        break;
      }
    }
    $user->group = $ranks[$newRank];
    $this->orm->users->persistAndFlush($user);
  }
}
?>