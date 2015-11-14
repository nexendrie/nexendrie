<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Castle as CastleEntity;

/**
 * Castle Model
 *
 * @author Jakub Konečný
 */
class Castle extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  protected $buildingPrice;
  
  function __construct($buildingPrice, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
    $this->buildingPrice = (int) $buildingPrice;
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
    if($user->group->path != "tower") throw new CannotBuildCastleException;
    elseif($this->getUserCastle()) throw new CannotBuildMoreCastlesException;
    elseif($this->orm->castles->getByName($data["name"])) throw new CastleNameInUseException;
    elseif($user->money < $this->buildingPrice) throw new InsufficientFundsException;
    $castle = new CastleEntity;
    $castle->name = $data["name"];
    $castle->description = $data["description"];
    $castle->founded = time();
    $castle->owner = $user;
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
   * Calculate upgrade price of castle
   * 
   * @return int
   */
  function calculateUpgradePrice() {
    if(!$this->user->isLoggedIn()) return 0;
    $castle = $this->orm->castles->getByOwner($this->user->id);
    if(!$castle) return 0;
    else return $castle->upgradePrice;
  }
  
  /**
   * Upgrade castle
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws CannotUpgradeCastle
   * @throws InsufficientFundsException
   */
  function upgrade() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif(!$this->canUpgrade()) throw new CannotUpgradeCastle;
    $castle = $this->orm->castles->getByOwner($this->user->id);
    if($castle->owner->money < $castle->upgradePrice) throw new InsufficientFundsException;
    $castle->owner->money -= $castle->upgradePrice;
    $castle->level++;
    $this->orm->castles->persistAndFlush($castle);
  }
}

class CastleNotFoundException extends RecordNotFoundException {
  
}

class CannotBuildCastleException extends AccessDeniedException {
  
}

class CannotBuildMoreCastlesException extends AccessDeniedException {
  
}

class CastleNameInUseException extends \RuntimeException {
  
}

class CannotUpgradeCastle extends AccessDeniedException {
  
}
?>