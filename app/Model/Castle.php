<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Castle as CastleEntity,
    Nexendrie\Orm\Group as GroupEntity;

/**
 * Castle Model
 *
 * @author Jakub Konečný
 * @property-read int $buildingPrice
 */
class Castle {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  protected $buildingPrice;
  
  use \Nette\SmartObject;
  
  function __construct($buildingPrice, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
    $this->buildingPrice = (int) $buildingPrice;
  }
  
  /**
   * @return int
   */
  function getBuildingPrice() {
    return $this->buildingPrice;
  }
  
  /**
   * Get list of all castles
   * 
   * @return CastleEntity[]
   */
  function listOfCastles() {
    return $this->orm->castles->findAll();
  }
  
  /**
   * Get details of specified castle
   * 
   * @param int $id
   * @return CastleEntity
   * @throws CastleNotFoundException
   */
  function getCastle($id) {
    $castle = $this->orm->castles->getById($id);
    if(!$castle) throw new CastleNotFoundException;
    else return $castle;
  }
  
  /**
   * Check whetever a name can be used
   * 
   * @param string $name
   * @param int|NULL $id
   * @return bool
   */
  private function checkNameAvailability($name, $id = NULL) {
    $castle = $this->orm->castles->getByName($name);
    if($castle AND $castle->id != $id) return false;
    else return true;
  }
  
  /**
   * Edit specified castle
   * 
   * @param int $id
   * @param array $data
   * @return void
   * @throws CastleNotFoundException
   * @throws CastleNameInUseException
   */
  function editCastle($id, array $data) {
    try {
      $castle = $this->getCastle($id);
    } catch(CastleNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      if($key === "name" AND !$this->checkNameAvailability($value, $id)) throw new CastleNameInUseException;
      $castle->$key = $value;
    }
    $this->orm->castles->persistAndFlush($castle);
  }
  
  /**
   * Build castle
   * 
   * @param array $data
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotBuildCastleException
   * @throws CannotBuildMoreCastlesException
   * @throws CastleNameInUseException
   * @throws InsufficientFundsException
   */
  function build(array $data) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->path != GroupEntity::PATH_TOWER) throw new CannotBuildCastleException;
    elseif($this->getUserCastle()) throw new CannotBuildMoreCastlesException;
    elseif(!$this->checkNameAvailability($data["name"])) throw new CastleNameInUseException;
    elseif($user->money < $this->buildingPrice) throw new InsufficientFundsException;
    $castle = new CastleEntity;
    $castle->name = $data["name"];
    $castle->description = $data["description"];
    $castle->owner = $user;
    $castle->owner->lastActive = time();
    $castle->owner->money -= $this->buildingPrice;
    $this->orm->castles->persistAndFlush($castle);
    $user->castle = $this->orm->castles->getByName($data["name"]);
    $this->orm->users->persistAndFlush($user);
  }
  
  /**
   * Get specified user's castle
   * 
   * @param int|NULL $user
   * @return CastleEntity
   */
  function getUserCastle($user = NULL) {
    if($user === NULL) $user = $this->user->id;
    return $this->orm->castles->getByOwner($user);
  }
  
  /**
   * Check whetever the user can upgrade castle
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canUpgrade() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $castle = $this->orm->castles->getByOwner($this->user->id);
    if(!$castle) return false;
    elseif($castle->level >= CastleEntity::MAX_LEVEL) return false;
    else return true;
  }
  
  /**
   * Upgrade castle
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeCastleException
   * @throws InsufficientFundsException
   */
  function upgrade() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canUpgrade()) throw new CannotUpgradeCastleException;
    $castle = $this->orm->castles->getByOwner($this->user->id);
    if($castle->owner->money < $castle->upgradePrice) throw new InsufficientFundsException;
    $castle->owner->money -= $castle->upgradePrice;
    $castle->level++;
    $this->orm->castles->persistAndFlush($castle);
  }
  
  /**
   * Check whetever the user can repair castle
   * 
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canRepair() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $castle = $this->orm->castles->getByOwner($this->user->id);
    if(!$castle) return false;
    elseif($castle->hp >= 100) return false;
    else return true;
  }
  
  /**
   * Repair castle
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotRepairCastleException
   * @throws InsufficientFundsException
   */
  function repair() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canRepair()) throw new CannotRepairCastleException;
    $castle = $this->orm->castles->getByOwner($this->user->id);
    if($castle->owner->money < $castle->repairPrice) throw new InsufficientFundsException;
    $castle->owner->money -= $castle->repairPrice;
    $castle->hp = 100;
    $this->orm->castles->persistAndFlush($castle);
  }
}
?>