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
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
    $this->buildingPrice = 1500;
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
}

class CastleNotFoundException extends RecordNotFoundException {
  
}

class CannotBuildCastleException extends AccessDeniedException {
  
}

class CannotBuildMoreCastlesException extends AccessDeniedException {
  
}

class CastleNameInUseException extends \RuntimeException {
  
}
?>