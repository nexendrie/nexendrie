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
class Adventure {
  /** @var \Nexendrie\Model\Combat */
  protected $combatModel;
  /** @var Events */
  protected $eventsModel;
  /** @var \Nexendrie\Model\Order */
  protected $orderModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var AdventureEntity */
  private $adventure = NULL;
  
  use \Nette\SmartObject;
  
  function __construct(Combat $combatModel, Events $eventsModel, Order $orderModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->combatModel = $combatModel;
    $this->eventsModel = $eventsModel;
    $this->orderModel = $orderModel;
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
   * @throws AdventureNotAccessibleException
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
    elseif($adventure->event AND !$adventure->event->active) throw new AdventureNotAccessibleException;
    $userAdventure = new UserAdventureEntity;
    $this->orm->userAdventures->attach($userAdventure);
    $userAdventure->user = $this->user->id;
    $userAdventure->adventure = $adventure;
    $userAdventure->mount = $mount;
    $this->orm->userAdventures->persistAndFlush($userAdventure);
    $this->user->identity->travelling = true;
  }
  
  /**
   * Get user's active adventure
   * 
   * @return UserAdventureEntity|NULL
   * @throws AuthenticationNeededException
   */
  function getCurrentAdventure() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    elseif($this->adventure) return $this->adventure;
    $this->adventure = $this->orm->userAdventures->getUserActiveAdventure($this->user->id);
    return $this->adventure;
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
   * @param MountEntity $mount  
   * @return bool Whetever the user won
   */
  protected function fightNpc(AdventureNpcEntity $npc, MountEntity $mount) {
    $finished = $result = false;
    $user = $this->orm->users->getById($this->user->id);
    $userStats = $this->combatModel->userCombatStats($user, $mount);
    $user->life += $userStats["maxLife"] - $user->maxLife;
    $npcLife = $npc->hitpoints;
    $userAttack = max($userStats["damage"] - $npc->armor, 0);
    $npcAttack = max($npc->strength - $userStats["armor"], 0);
    $round = 1;
    while(!$finished) {
      $npcLife -= $userAttack;
      if($npcLife <= 1) $finished = $result = true;
      $user->life -= $npcAttack;
      if($user->life <= 1) $finished = true;
      $round++;
      if($round > 30) $finished = true;
    }
    return $result;
  }
  
  protected function saveVictory(UserAdventureEntity $adventure, AdventureNpcEntity $enemy) {
    $reward = $enemy->reward;
    $reward += $this->eventsModel->calculateAdventuresBonus($reward);
    $reward += $this->orderModel->calculateOrderIncomeBonus($reward);
    $adventure->progress++;
    $adventure->user->money += $reward;
    $adventure->loot += $reward;
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
    $success = $this->fightNpc($enemy, $adventure->mount);
    if($success) {
      $message = $enemy->victoryText;
      $this->saveVictory($adventure, $enemy);
    } else {
      $message = "$enemy->name se ubránil.";
      $this->orm->users->persistAndFlush($adventure->user);
    }
    return ["success" => $success, "message" => $message];
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
    $reward = $adventure->adventure->reward;
    $reward += $this->eventsModel->calculateAdventuresBonus($reward);
    $reward += $this->orderModel->calculateOrderIncomeBonus($reward);
    $adventure->user->money += $reward;
    $adventure->reward += $reward;
    $adventure->mount->hp -= 5;
    $this->orm->userAdventures->persistAndFlush($adventure);
    $this->user->identity->travelling = false;
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
    $next = new \DateTime;
    $next->setTimestamp($adventure->fetch()->started + $twoDays);
    $next->setTime(0, 0, 0);
    if($next->getTimestamp() < time()) return true;
    return false;
  }
}
?>