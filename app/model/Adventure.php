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
  /** @var \Nexendrie\Model\Combat */
  protected $combatModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(Combat $combatModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->combatModel = $combatModel;
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
   * @throws AdventureNotFoundException
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
   * @return MountEntity[]
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
    if(!$this->canDoAdventure()) throw new CannotDoAdventureException;
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
  
  /**
   * Get next enemy for adventure
   * 
   * @param UserAdventureEntity $adventure
   * @return AdventureNpcEntity|NULL
   */
  function getNextNpc(UserAdventureEntity $adventure) {
    if($adventure->progress >= 9) return NULL;
    else return $this->orm->adventureNpcs->getByAdventureAndOrder($adventure->adventure->id, $adventure->progress + 1);
  }
  
  /**
   * Fight a npc
   * 
   * @param AdventureNpcEntity $npc
   * @return bool Whetever the user won
   */
  protected function fightNpc(AdventureNpcEntity $npc) {
    $finished = $result = false;
    $user = $this->orm->users->getById($this->user->id);
    $userStats = $this->combatModel->userCombatStats($this->user->id);
    $npcLife = $npc->hitpoints;
    $userAttack = $userStats["damage"] - $npc->armor;
    $npcAttack = $npc->strength - $userStats["armor"];
    while(!$finished) {
      $npcLife -= $userAttack;
      if($npcLife <= 1) {
        $finished = true;
        $result = true;
      }
      $user->life -= $npcAttack;
      if($user->life <= 1) $finished = true;
    }
    $this->orm->users->persistAndFlush($user);
    return $result;
  }
  
  protected function saveVictory(UserAdventureEntity $adventure, AdventureNpcEntity $enemy) {
    $adventure->progress++;
    $adventure->user->money += $enemy->reward;
    $adventure->loot += $enemy->reward;
    $this->orm->userAdventures->persistAndFlush($adventure);
  }
  
  /**
   * Fight next enemy
   * 
   * @return array
   * @throws AuthenticationNeededException
   * @throws NotOnAdventureException
   * @throws NoEnemyRemainException
   */
  function fight() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $adventure = $this->getCurrentAdventure();
    if(!$adventure) throw new NotOnAdventureException;
    if($adventure->progress > 9) throw new NoEnemyRemainException;
    $enemy = $this->orm->adventureNpcs->getByAdventureAndOrder($adventure->adventure->id, $adventure->progress + 1);
    if(!$enemy) throw new NoEnemyRemainException;
    $success = $this->fightNpc($enemy);
    if($success) {
      $message = $enemy->victoryText;
      $this->saveVictory($adventure, $enemy);
    } else {
      $message = "$enemy->name se ubránil.";
    }
    return array("success" => $success, "message" => $message);
  }
  
  /**
   * Finish adventure
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws NotOnAdventureException
   * @throws NotAllEnemiesDefeateException
   */
  function finishAdventure() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $adventure = $this->getCurrentAdventure();
    if(!$adventure) throw new NotOnAdventureException;
    if($this->getNextNpc($adventure)) throw new NotAllEnemiesDefeateException;
    $adventure->progress = 10;
    $adventure->user->money += $adventure->adventure->reward;
    $adventure->reward += $adventure->adventure->reward;
    $adventure->mount->hp -= 5;
    $this->orm->userAdventures->persistAndFlush($adventure);
  }
  
  /**
   * Calculate income from user's adventures from a month
   * 
   * @param int $user
   * @param int $month
   * @param int $year
   * @return int
   */
  function calculateMonthAdventuresIncome($user = 0, $month = 0, $year = 0) {
    $income = 0;
    if($user === 0) $user = $this->user->id;
    $adventures = $this->orm->userAdventures->findFromMonth($user, $month, $year);
    foreach($adventures as $adventure) {
      $income += $adventure->reward + $adventure->loot;
    }
    return $income;
  }
  
  /**
   * @return bool
   * @throws AuthenticationNeededException
   */
  function canDoAdventure() {
    $twoDays = 60 * 60 * 24 * 2;
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $adventure = $this->orm->userAdventures->getLastAdventure($this->user->id);
    if(!$adventure->count()) return true;
    elseif($adventure->fetch()->started + $twoDays < time()) return true;
    else return false;
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

class NotOnAdventureException extends AccessDeniedException {
  
}

class NoEnemyRemainException extends AccessDeniedException {
  
}

class NotAllEnemiesDefeateException extends AccessDeniedException {
  
}

class CannotDoAdventureException extends AccessDeniedException {
  
}
?>