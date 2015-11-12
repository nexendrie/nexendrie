<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Mount as MountEntity,
    Nexendrie\Orm\MountType as MountTypeEntity;

/**
 * Mount Model
 *
 * @author Jakub Konečný
 */
class Mount extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get specified mount
   * 
   * @param int $id Mount's id
   * @return MountEntity
   * @throws MountNotFoundException
   */
  function get($id) {
    $mount = $this->orm->mounts->getById($id);
    if(!$mount) throw new MountNotFoundException;
    else return $mount;
  }
  
  /**
   * Get list of all mounts
   * 
   * @param int|NULL $owner Return only mounts owned by specified use. NULL = all users
   * @return MountEntity[]
   */
  function listOfMounts($owner = NULL) {
    if(is_int($owner)) return $this->orm->mounts->findByOwner($owner);
    else return $this->orm->mounts->findAll();
  }
  
  /**
   * Get mounts on sale
   * 
   * @return MountEntity[]
   */
  function mountsOnSale() {
    return $this->orm->mounts->findOnMarket();
  }
  
  /**
   * Get list of all mount types
   * 
   * @return MountTypeEntity[]
   */
  function listOfMountTypes() {
    return $this->orm->mountTypes->findAll();
  }
  
  /**
   * Add new mount
   * 
   * @param array $data
   * @return void
   */
  function add(array $data) {
    $mount = new MountEntity;
    $this->orm->mounts->attach($mount);
    foreach($data as $key => $value) {
      $mount->$key = $value;
    }
    $mount->owner = 0;
    $mount->birth = time();
    $mount->onMarket = 1;
    $this->orm->mounts->persistAndFlush($mount);
  }
  
  /**
   * Edit specified mount
   * 
   * @param int $id Mount's id
   * @param array $data
   * @return void
   */
  function edit($id, array $data) {
    $mount = $this->orm->mounts->getById($id);
    foreach($data as $key => $value) {
      $mount->$key = $value;
    }
    $this->orm->mounts->persistAndFlush($mount);
  }
  
  /**
   * Buy specified mount
   * 
   * @param int $id Mount's id
   * @return void
   * @throws AuthenticationNeededException
   * @throws MountNotFoundException
   * @throws MountNotOnSaleException
   * @throws CannotBuyOwnMountException
   * @throws InsufficientLevelForMountException
   * @throws InsufficientFundsException
   */
  function buy($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $mount = $this->orm->mounts->getById($id);
    if(!$mount) throw new MountNotFoundException;
    if(!$mount->onMarket) throw new MountNotOnSaleException;
    if($mount->owner->id === $this->user->id) throw new CannotBuyOwnMountException;
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->level < $mount->type->level) throw new InsufficientLevelForMountException;
    if($user->money < $mount->price) throw new InsufficientFundsException;
    $seller = $mount->owner;
    $seller->money += $mount->price;
    $this->orm->users->persist($seller);
    $user->money -= $mount->price;
    $user->lastActive = time();
    $mount->owner = $user;
    $mount->onMarket = false;
    $this->orm->mounts->persist($mount);
    $this->orm->flush();
  }
}

class MountNotFoundException extends RecordNotFoundException {
  
}

class MountNotOnSaleException extends AccessDeniedException {
  
}

class InsufficientLevelForMountException extends AccessDeniedException {
  
}

class CannotBuyOwnMountException extends AccessDeniedException {
  
}

class MountNotOwnedException extends AccessDeniedException {
  
}

class CareNotNeededException extends AccessDeniedException {
  
}

class MountInBadConditionException extends AccessDeniedException {
  
}
?>