<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Monastery as MonasteryEntity,
    Nexendrie\Orm\MonasteryDonation,
    Nextras\Orm\Collection\ICollection;

/**
 * Monastery Model
 *
 * @author Jakub Konečný
 */
class Monastery extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  protected $buildingPrice;
  
  const MAX_LEVEL = 6;
  const BASE_UPGRADE_PRICE = 700;
  
  /**
   * @param int $buildingPrice
   * @param \Nexendrie\Orm\Model $orm
   * @param \Nette\Security\User $user
   */
  function __construct($buildingPrice, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
    $this->buildingPrice = $buildingPrice;
  }
  
  /**
   * Get list of all monasteries
   * 
   * @return MonasteryEntity[]
   */
  function listOfMonasteries() {
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
  function get($id) {
    $monastery = $this->orm->monasteries->getById($id);
    if(!$monastery) throw new MonasteryNotFoundException;
    else return $monastery;
  }
  
  /**
   * Get specified user's monastary
   * 
   * @param int $id
   * @return MonasteryEntity
   * @throws UserNotFoundException
   * @throws NotInMonasteryException
   */
  function getByUser($id = 0) {
    if($id === 0) $id = $this->user->id;
    $user = $this->orm->users->getById($id);
    if(!$user) throw new UserNotFoundException;
    elseif(!$user->monastery) throw new NotInMonasteryException;
    else return $user->monastery;
  }
  
  /**
   * Check whetever the user can join a monastery
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canJoin() {
    $month = 60 * 60 * 24 * 31;
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery AND $user->group->path === "city") return true;
    elseif($user->group->path === "church" AND $user->monasteriesLed->countStored()) return false;
    elseif($user->group->path === "church" AND $user->lastTransfer === NULL) return true;
    elseif($user->group->path === "church" AND $user->lastTransfer  + $month < time()) return true;
    else return false;
  }
  
  /**
   * Join a monastery
   * 
   * @param int $id
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotJoinMonasteryException
   * @throws MonasteryNotFoundException
   * @throws CannotJoinOwnMonastery
   */
  function join($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canJoin()) throw new CannotJoinMonasteryException;
    try {
      $monastery = $this->get($id);
    } catch(MonasteryNotFoundException $e) {
      throw $e;
    }
    $user = $this->orm->users->getById($this->user->id);
    if($user->monastery->id === $monastery->id) throw new CannotJoinOwnMonastery;
    $user->lastTransfer = $user->lastActive = time();
    $user->monastery = $monastery;
    if($user->group->path != "church") $user->group = $this->orm->groups->getByLevel(55);
    $user->town = $monastery->town;
    $this->orm->users->persistAndFlush($user);
    $this->user->identity->group = $user->group->id;
    $this->user->identity->level = $user->group->level;
    $this->user->identity->town = $user->town->id;
    $this->user->identity->roles = array($user->group->singleName);
  }
  
  /**
   * Check whetever the user can pray (now)
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canPray() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery) return false;
    elseif($user->life >= $user->maxLife) return false;
    elseif(!$user->lastPrayer) return true;
    $oneDay = 60 * 60 * 24;
    if($user->lastPrayer + $oneDay < time()) return true;
    else return false;
  }
  
  /**
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotPrayException
   */
  function pray() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    if(!$this->canPray()) throw new CannotPrayException;
    $user = $this->orm->users->getById($this->user->id);
    $user->lastPrayer = time();
    $user->life += 4 + $user->monastery->level;
    $user->prayers++;
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Check whetever the user can leave monastery
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canLeave( ) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery) return false;
    elseif($user->id === $user->monastery->leader->id) return false;
    else return true;
  }
  
  /**
   * Leave monastery
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotLeaveMonasteryException
   */
  function leave() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    if(!$this->canLeave()) throw new CannotLeaveMonasteryException;
    $user = $this->orm->users->getById($this->user->id);
    $user->monastery = NULL;
    if($user->ownedTowns->countStored()) $user->group = $this->orm->groups->getByLevel(400);
    else $user->group = $this->orm->groups->getByLevel(50);
    $this->orm->users->persistAndFlush($user);
    $this->user->identity->group = $user->group->id;
    $this->user->identity->level = $user->group->level;
    $this->user->identity->roles = array($user->group->singleName);
  }
  
  /**
   * Check whetever the user can build monastery
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canBuild() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    if($this->user->identity->level != 550) return false;
    $user = $this->orm->users->getById($this->user->id);
    if($user->monasteriesLed->countStored() > 0) return false;
    else return true;
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
  function build($name) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    if(!$this->canBuild()) throw new CannotBuildMonasteryException;
    if($this->orm->monasteries->getByName($name)) throw new MonasteryNameInUseException;
    $user = $this->orm->users->getById($this->user->id);
    if($user->money < $this->buildingPrice) throw new InsufficientFundsException;
    $monastery = new MonasteryEntity;
    $this->orm->monasteries->attach($monastery);
    $monastery->name = (string) $name;
    $monastery->leader = $user;
    $monastery->town = $this->user->identity->town;
    $monastery->founded = time();
    $user->money -= $this->buildingPrice;
    $monastery->money = $this->buildingPrice;
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
  function donate($amount) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery) throw new NotInMonasteryException;
    elseif($user->money < $amount) throw new InsufficientFundsException;
    $user->money -= $amount;
    $user->monastery->money += $amount;
    $donation = new MonasteryDonation;
    $donation->user = $user;
    $donation->monastery = $user->monastery;
    $donation->amount = $amount;
    $donation->when = time();
    $this->orm->monasteryDonations->persistAndFlush($donation);
  }
  
  /**
   * Edit specified monastery
   * 
   * @param int $id
   * @param array $data
   * @throws MonasteryNotFoundException
   * @throws MonasteryNameInUseException
   */
  function edit($id, array $data) {
    $monastery = $this->orm->monasteries->getById($id);
    if(!$monastery) throw new MonasteryNotFoundException;
    $skip = array("town", "founded", "money");
    foreach($data as $key => $value) {
      if($key === "name") {
        $m = $this->orm->monasteries->getByName($value);
        if($m AND $m->id != $id) throw new MonasteryNameInUseException;
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
   * @param int $id
   * @return string[] id => publicname
   */
  function highClerics($id) {
    return $this->orm->users->findByMonastery($id)
      ->findBy(array("this->group->level" => 550))
      ->fetchPairs("id", "publicname");
  }
  
  /**
   * Check whetever the user can manage monastery
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canManage() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery) return false;
    elseif($user->monastery->leader->id != $this->user->id) return false;
    else return true;
  }
  
  /**
   * How many hitpoints will the prayer add
   * 
   * @return int
   */
  function prayerLife() {
    if(!$this->user->isLoggedIn()) return 0;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery) return 0;
    return 2 + ($user->monastery->level * 2);
  }
  
  /**
   * Calculate price of monastery's next upgrade
   * 
   * @return int
   */
  function calculateUpgradePrice() {
    if(!$this->user->isLoggedIn()) return 0;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery) return 0;
    elseif($user->monastery->level < 2) return self::BASE_UPGRADE_PRICE;
    elseif($user->monastery->level >= self::MAX_LEVEL) return 0;
    $price = self::BASE_UPGRADE_PRICE;
    for($i = 2; $i < $user->monastery->level + 1; $i++) {
      $price += (int) (self::BASE_UPGRADE_PRICE / self::MAX_LEVEL);
    }
    return $price;
  }
  
  /**
   * Check whetever the user can manage monastery
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canUpgrade() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $user = $this->orm->users->getById($this->user->id);
    if(!$user->monastery) return false;
    elseif($user->monastery->leader->id != $this->user->id) return false;
    elseif($user->monastery->level >= self::MAX_LEVEL) return false;
    else return true;
  }
  
  /**
   * Upgrade monastery
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeMonasteryException
   * @throws InsufficientFundsException
   */
  function upgrade() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    if(!$this->canUpgrade()) throw new CannotUpgradeMonasteryException;
    $user = $this->orm->users->getById($this->user->id);
    $upgradePrice = $this->calculateUpgradePrice();
    if($user->monastery->money < $upgradePrice) throw new InsufficientFundsException;
    $user->monastery->level++;
    $user->monastery->money -= $upgradePrice;
    $this->orm->monasteries->persistAndFlush($user->monastery);
  }
}

class MonasteryNotFoundException extends RecordNotFoundException {
  
}

class NotInMonasteryException extends AccessDeniedException {
  
}

class CannotJoinMonasteryException extends AccessDeniedException {
  
}

class CannotPrayException extends AccessDeniedException {
  
}

class CannotLeaveMonasteryException extends AccessDeniedException {
  
}

class CannotBuildMonasteryException extends AccessDeniedException {
  
}

class MonasteryNameInUseException extends \RuntimeException {
  
}

class CannotJoinOwnMonastery extends AccessDeniedException {
  
}

class CannotUpgradeMonasteryException extends AccessDeniedException {

}
?>