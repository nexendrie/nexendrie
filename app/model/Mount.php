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
   * @return MountEntity[]
   */
  function listOfMounts() {
    return $this->orm->mounts->findAll();
  }
  
  /**
   * Get list of all mount types
   * 
   * @return MountTypeEntity
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
    $mount->gender = MountEntity::GENDER_YOUNG;
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
}

class MountNotFoundException extends RecordNotFoundException {
  
}
?>