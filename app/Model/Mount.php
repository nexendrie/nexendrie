<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Mount as MountEntity,
    Nexendrie\Orm\MountType as MountTypeEntity,
    Nextras\Orm\Collection\ICollection;

/**
 * Mount Model
 *
 * @author Jakub Konečný
 */
class Mount {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  use \Nette\SmartObject;
  
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
  function get(int $id): MountEntity {
    $mount = $this->orm->mounts->getById($id);
    if(is_null($mount)) {
      throw new MountNotFoundException;
    }
    return $mount;
  }
  
  /**
   * Get list of all mounts
   * 
   * @param int $owner Return only mounts owned by specified user. NULL = all users
   * @return MountEntity[]|ICollection
   */
  function listOfMounts(int $owner = NULL): ICollection {
    if(is_int($owner)) {
      return $this->orm->mounts->findByOwner($owner);
    }
    return $this->orm->mounts->findAll();
  }
  
  /**
   * Get mounts on sale
   * 
   * @return MountEntity[]|ICollection
   */
  function mountsOnSale(): ICollection {
    return $this->orm->mounts->findOnMarket()
      ->orderBy("this->type->id", ICollection::DESC)
      ->orderBy("price", ICollection::DESC);
  }
  
  /**
   * Get list of all mount types
   * 
   * @return MountTypeEntity[]|ICollection
   */
  function listOfMountTypes(): ICollection {
    return $this->orm->mountTypes->findAll();
  }
  
  /**
   * Add new mount
   * 
   * @param array $data
   * @return void
   */
  function add(array $data): void {
    $mount = new MountEntity;
    $this->orm->mounts->attach($mount);
    foreach($data as $key => $value) {
      $mount->$key = $value;
    }
    $mount->owner = 0;
    $this->orm->mounts->persistAndFlush($mount);
  }
  
  /**
   * Edit specified mount
   * 
   * @param int $id Mount's id
   * @param array $data
   * @return void
   * @throws MountNotFoundException
   */
  function edit(int $id, array $data): void {
    try {
      $mount = $this->get($id);
    } catch(MountNotFoundException $e) {
      throw $e;
    }
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
  function buy(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    try {
      $mount = $this->get($id);
    } catch(MountNotFoundException $e) {
      throw $e;
    }
    if(!$mount->onMarket) {
      throw new MountNotOnSaleException;
    }
    if($mount->owner->id === $this->user->id) {
      throw new CannotBuyOwnMountException;
    }
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->level < $mount->type->level) {
      throw new InsufficientLevelForMountException;
    }
    if($user->money < $mount->price) {
      throw new InsufficientFundsException;
    }
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
?>