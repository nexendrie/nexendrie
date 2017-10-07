<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Adventure as AdventureEntity,
    Nexendrie\Orm\AdventureNpc as AdventureNpcEntity,
    Nexendrie\Orm\UserAdventure as UserAdventureEntity,
    Nexendrie\Orm\Mount as MountEntity,
    Nextras\Orm\Collection\ICollection,
    Nextras\Orm\Relationships\OneHasMany;

/**
 * Adventure Model
 *
 * @author Jakub Konečný
 */
class Adventure {
  /** @var Combat */
  protected $combatModel;
  /** @var Events */
  protected $eventsModel;
  /** @var Order */
  protected $orderModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var UserAdventureEntity|null */
  private $adventure = NULL;
  
  use \Nette\SmartObject;
  
  public function __construct(Combat $combatModel, Events $eventsModel, Order $orderModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->combatModel = $combatModel;
    $this->eventsModel = $eventsModel;
    $this->orderModel = $orderModel;
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all adventures
   * 
   * @return AdventureEntity[]|ICollection
   */
  public function listOfAdventures() {
    return $this->orm->adventures->findAll();
  }
  
  /**
   * Get npcs from specified adventure
   *
   * @return AdventureNpcEntity[]|OneHasMany
   * @throws AdventureNotFoundException
   */
  public function listOfNpcs(int $adventureId): OneHasMany {
    $adventure = $this->orm->adventures->getById($adventureId);
    if(is_null($adventure)) {
      throw new AdventureNotFoundException();
    }
    return $adventure->npcs;
  }
  
  /**
   * Get specified adventure
   *
   * @throws AdventureNotFoundException
   */
  public function get(int $id): AdventureEntity {
    $adventure = $this->orm->adventures->getById($id);
    if(is_null($adventure)) {
      throw new AdventureNotFoundException();
    }
    return $adventure;
  }
  
  /**
   * Add new adventure
   */
  public function addAdventure(array $data): void {
    $adventure = new AdventureEntity();
    foreach($data as $key => $value) {
      $adventure->$key = $value;
    }
    $this->orm->adventures->persistAndFlush($adventure);
  }
  
  /**
   * Edit adventure
   *
   * @throws AdventureNotFoundException
   */
  public function editAdventure(int $id, array $data): void {
    try {
      $adventure = $this->get($id);
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
   * @throws AdventureNpcNotFoundException
   */
  public function getNpc(int $id): AdventureNpcEntity {
    $npc = $this->orm->adventureNpcs->getById($id);
    if(is_null($npc)) {
      throw new AdventureNpcNotFoundException();
    }
    return $npc;
  }
  
  /**
   * Add new npc
   */
  public function addNpc(array $data): void {
    $npc = new AdventureNpcEntity();
    $this->orm->adventureNpcs->attach($npc);
    foreach($data as $key => $value) {
      $npc->$key = $value;
    }
    $this->orm->adventureNpcs->persistAndFlush($npc);
  }
  
  /**
   * Edit specified npc
   *
   * @throws AdventureNpcNotFoundException
   */
  public function editNpc(int $id, array $data): void {
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
   * @throws AdventureNpcNotFoundException
   */
  public function deleteNpc(int $id): int {
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
   * @return AdventureEntity[]|ICollection
   * @throws AuthenticationNeededException
   */
  public function findAvailableAdventures(): ICollection {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    return $this->orm->adventures->findForLevel($this->user->identity->level);
  }
  
  /**
   * Find mounts for adventure
   * 
   * @return MountEntity[]|ICollection
   * @throws AuthenticationNeededException
   */
  public function findGoodMounts(): ICollection {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    return $this->orm->mounts->findGoodMounts($this->user->id);
  }
  
  /**
   * Start an adventure
   *
   * @throws AuthenticationNeededException
   * @throws AlreadyOnAdventureException
   * @throws AdventureNotFoundException
   * @throws InsufficientLevelForAdventureException
   * @throws MountNotFoundException
   * @throws MountNotOwnedException
   * @throws MountInBadConditionException
   * @throws AdventureNotAccessibleException
   */
  public function startAdventure(int $adventureId, int $mountId): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    if($this->getCurrentAdventure()) {
      throw new AlreadyOnAdventureException();
    }
    if(!$this->canDoAdventure()) {
      throw new CannotDoAdventureException();
    }
    $adventure = $this->orm->adventures->getById($adventureId);
    if(is_null($adventure)) {
      throw new AdventureNotFoundException();
    }
    if($adventure->level > $this->user->identity->level) {
      throw new InsufficientLevelForAdventureException();
    }
    $mount = $this->orm->mounts->getById($mountId);
    if(is_null($mount)) {
      throw new MountNotFoundException();
    } elseif($mount->owner->id != $this->user->id) {
      throw new MountNotOwnedException();
    } elseif($mount->hp < 30) {
      throw new MountInBadConditionException();
    } elseif($adventure->event AND !$adventure->event->active) {
      throw new AdventureNotAccessibleException();
    }
    $userAdventure = new UserAdventureEntity();
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
   * @throws AuthenticationNeededException
   */
  public function getCurrentAdventure(): ?UserAdventureEntity {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif($this->adventure) {
      return $this->adventure;
    }
    $this->adventure = $this->orm->userAdventures->getUserActiveAdventure($this->user->id);
    return $this->adventure;
  }
  
  /**
   * Get next enemy for adventure
   */
  public function getNextNpc(UserAdventureEntity $adventure): ?AdventureNpcEntity {
    if($adventure->progress >= 9) {
      return NULL;
    }
    return $this->orm->adventureNpcs->getByAdventureAndOrder($adventure->adventure->id, $adventure->progress + 1);
  }
  
  /**
   * Fight a npc
   *
   * @return bool Whether the user won
   */
  protected function fightNpc(AdventureNpcEntity $npc, MountEntity $mount): bool {
    $finished = $result = false;
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    $userStats = $this->combatModel->userCombatStats($user, $mount);
    $user->life += $userStats["maxLife"] - $user->maxLife;
    $npcLife = $npc->hitpoints;
    $userAttack = max($userStats["damage"] - $npc->armor, 0);
    $npcAttack = max($npc->strength - $userStats["armor"], 0);
    $round = 1;
    while(!$finished) {
      $npcLife -= $userAttack;
      if($npcLife <= 1) {
        $finished = $result = true;
      }
      $user->life -= $npcAttack;
      if($user->life <= 1) {
        $finished = true;
      }
      $round++;
      if($round > 30) {
        $finished = true;
      }
    }
    return $result;
  }
  
  protected function saveVictory(UserAdventureEntity $adventure, AdventureNpcEntity $enemy): void {
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
   * @throws AuthenticationNeededException
   * @throws NotOnAdventureException
   * @throws NoEnemyRemainException
   */
  public function fight(): array {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $adventure = $this->getCurrentAdventure();
    if(is_null($adventure)) {
      throw new NotOnAdventureException();
    }
    if($adventure->progress > 9) {
      throw new NoEnemyRemainException();
    }
    $enemy = $this->orm->adventureNpcs->getByAdventureAndOrder($adventure->adventure->id, $adventure->progress + 1);
    if(is_null($enemy)) {
      throw new NoEnemyRemainException();
    }
    $success = $this->fightNpc($enemy, $adventure->mount);
    if($success) {
      $this->saveVictory($adventure, $enemy);
      return ["success" => true, "message" => $enemy->victoryText];
    }
    $this->orm->users->persistAndFlush($adventure->user);
    return ["success" => false, "message" => "$enemy->name se ubránil."];
  }
  
  /**
   * Finish adventure
   *
   * @throws AuthenticationNeededException
   * @throws NotOnAdventureException
   * @throws NotAllEnemiesDefeatedException
   */
  public function finishAdventure(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $adventure = $this->getCurrentAdventure();
    if(is_null($adventure)) {
      throw new NotOnAdventureException();
    }
    if($this->getNextNpc($adventure)) {
      throw new NotAllEnemiesDefeatedException();
    }
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
   */
  public function calculateMonthAdventuresIncome(int $user = NULL, int $month = NULL, int $year = NULL): int {
    $income = 0;
    $adventures = $this->orm->userAdventures->findFromMonth($user ?? $this->user->id, $month, $year);
    foreach($adventures as $adventure) {
      $income += $adventure->reward + $adventure->loot;
    }
    return $income;
  }
  
  /**
   * @throws AuthenticationNeededException
   */
  public function canDoAdventure(): bool {
    $twoDays = 60 * 60 * 24 * 2;
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $adventure = $this->orm->userAdventures->getLastAdventure($this->user->id);
    if(is_null($adventure)) {
      return true;
    }
    $next = new \DateTime;
    $next->setTimestamp($adventure->started + $twoDays);
    $next->setTime(0, 0, 0);
    return ($next->getTimestamp() < time());
  }
}
?>