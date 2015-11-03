<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Adventure as AdventureEntity,
    Nexendrie\Orm\AdventureNpc as AdventureNpcEntity,
    Nexendrie\Orm\UserAdventure as UserAdventureEntity,
    Nexendrie\Orm\Mount as MountEntity;

/**
 * Adventure Model
 *
 * @author Jakub Konečný
 */
class Adventure extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all adventures
   * 
   * @return AdventureEntity[]
   */
  function listOfAdventures() {
    return $this->orm->adventures->findAll();
  }
  
  /**
   * Get npcs from specified adventure
   * 
   * @param int $adventureId
   * @return AdventureNpcEntity[]
   * @throws AdventureNotFoundException
   */
  function listOfNpcs($adventureId) {
    $adventure = $this->orm->adventures->getById($adventureId);
    if(!$adventure) throw new AdventureNotFoundException;
    else return $adventure->npcs;
  }
  
  /**
   * Get specified adventure
   *  
   * @param int $id
   * @return AdventureEntity
   * @throws AdventureNotFoundException
   */
  function get($id) {
    $adventure = $this->orm->adventures->getById($id);
    if(!$adventure) throw new AdventureNotFoundException;
    else return $adventure;
  }
  
  /**
   * Add new adventure
   * 
   * @param array $data
   * @return void
   */
  function addAdventure(array $data) {
    $adventure = new AdventureEntity;
    foreach($data as $key => $value) {
      $adventure->$key = $value;
    }
    $this->orm->adventures->persistAndFlush($adventure);
  }
  
  /**
   * Edit adventure
   * 
   * @param int $id
   * @param array $data
   * @return void
   * @throws \Nexendrie\Model\AdventureNotFoundException
   */
  function editAdventure($id, array $data) {
    try {
      $adventure = $this->orm->adventures->getById($id);
    } catch(AdventureNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      $adventure->$key = $value;
    }
    $this->orm->adventures->persistAndFlush($adventure);
  }
  
  /**
   * Get specified npc
   * 
   * @param int $id
   * @return AdventureNpcEntity
   * @throws AdventureNpcNotFoundException
   */
  function getNpc($id) {
    $npc = $this->orm->adventureNpcs->getById($id);
    if(!$npc) throw new AdventureNpcNotFoundException;
    else return $npc;
  }
  
  /**
   * Add new npc
   * 
   * @param array $data
   * @return void
   */
  function addNpc(array $data) {
    $npc = new AdventureNpcEntity;
    $this->orm->adventureNpcs->attach($npc);
    foreach($data as $key => $value) {
      $npc->$key = $value;
    }
    $this->orm->adventureNpcs->persistAndFlush($npc);
  }
  
  /**
   * Edit specified npc
   * 
   * @param int $id
   * @param array $data
   * @return void
   * @throws \Nexendrie\Model\AdventureNpcNotFoundException
   */
  function editNpc($id, array $data) {
    try {
      $npc = $this->getNpc($id);
    } catch(AdventureNpcNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      $npc->$key = $value;
    }
    $this->orm->adventureNpcs->persistAndFlush($npc);
  }
  
  /**
   * Remove specified npc
   * 
   * @param int $id
   * @return int
   * @throws \Nexendrie\Model\AdventureNpcNotFoundException
   */
  function deleteNpc($id) {
    try {
      $npc = $this->getNpc($id);
    } catch(AdventureNpcNotFoundException $e) {
      throw $e;
    }
    $return = $npc->adventure->id;
    $this->orm->adventureNpcs->removeAndFlush($npc);
    return $return;
  }
  
  /**
   * Find available adventures for user
   * 
   * @return AdventureEntity[]
   * @throws AuthenticationNeededException
   */
  function findAvailableAdventures() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    else return $this->orm->adventures->findForLevel($this->user->identity->level);
  }
  
  /**
   * Find mounts for adventure
   * 
   * @return MountEntityp[]
   * @throws AuthenticationNeededException
   */
  function findGoodMounts() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    return $this->orm->mounts->findGoodMounts($this->user->id);
  }
  
  /**
   * Start an adventure
   * 
   * @param int $adventureId
   * @param int $mountId
   * @return void
   * @throws AuthenticationNeededException
   * @throws AlreadyOnAdventureException
   * @throws AdventureNotFoundException
   * @throws InsufficientLevelForAdventureException
   * @throws MountNotFoundException
   * @throws MountNotOwnedException
   * @throws MountInBadConditionException
   */
  function startAdventure($adventureId, $mountId) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    if($this->getCurrentAdventure()) throw new AlreadyOnAdventureException; 
    $adventure = $this->orm->adventures->getById($adventureId);
    if(!$adventure) throw new AdventureNotFoundException;
    if($adventure->level > $this->user->identity->level) throw new InsufficientLevelForAdventureException;
    $mount = $this->orm->mounts->getById($mountId);
    if(!$mount) throw new MountNotFoundException;
    elseif($mount->owner->id != $this->user->id) throw new MountNotOwnedException;
    elseif($mount->hp < 30) throw new MountInBadConditionException;
    $userAdventure = new UserAdventureEntity;
    $this->orm->userAdventures->attach($userAdventure);
    $userAdventure->user = $this->user->id;
    $userAdventure->adventure = $adventure;
    $userAdventure->mount = $mount;
    $userAdventure->started = time();
    $this->orm->userAdventures->persistAndFlush($userAdventure);
  }
  
  /**
   * Get user's active adventure
   * 
   * @return UserAdventureEntity|NULL
   * @throws AuthenticationNeededException
   */
  function getCurrentAdventure() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    else return $this->orm->userAdventures->getUserActiveAdventure($this->user->id);
  }
}

class AdventureNotFoundException extends RecordNotFoundException {
  
}

class AdventureNpcNotFoundException extends RecordNotFoundException {
  
}

class AlreadyOnAdventureException extends AccessDeniedException {
  
}

class InsufficientLevelForAdventureException extends AccessDeniedException {
  
}
?>