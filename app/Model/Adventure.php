<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Adventure as AdventureEntity;
use Nexendrie\Orm\AdventureNpc as AdventureNpcEntity;
use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\User;
use Nexendrie\Orm\UserAdventure as UserAdventureEntity;
use Nexendrie\Orm\Mount as MountEntity;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Relationships\OneHasMany;
use HeroesofAbenez\Combat\CombatBase;
use HeroesofAbenez\Combat\VictoryConditions;

/**
 * Adventure Model
 *
 * @author Jakub Konečný
 */
final class Adventure {
  public const ADVENTURE_BREAK_DAYS_LENGTH = 2;

  private ?UserAdventureEntity $adventure = null;
  
  public function __construct(private readonly CombatBase $combat, private readonly CombatHelper $combatHelper, private readonly Events $eventsModel, private readonly Order $orderModel, private readonly ORM $orm, private readonly \Nette\Security\User $user) {
  }
  
  /**
   * Get list of all adventures
   * 
   * @return AdventureEntity[]|ICollection
   */
  public function listOfAdventures(): ICollection {
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
    if($adventure === null) {
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
    return $adventure ?? throw new AdventureNotFoundException();
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
    return $npc ?? throw new AdventureNpcNotFoundException();
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
    return $this->orm->adventures->findForLevel($this->user->identity->level)->orderBy([
      "level" => ICollection::ASC, "reward" => ICollection::ASC,
    ]);
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
    if($this->getCurrentAdventure() !== null) {
      throw new AlreadyOnAdventureException();
    }
    if(!$this->canDoAdventure()) {
      throw new CannotDoAdventureException();
    }
    $adventure = $this->orm->adventures->getById($adventureId);
    if($adventure === null) {
      throw new AdventureNotFoundException();
    }
    if($adventure->level > $this->user->identity->level) {
      throw new InsufficientLevelForAdventureException();
    }
    $mount = $this->orm->mounts->getById($mountId);
    if($mount === null) {
      throw new MountNotFoundException();
    } elseif($mount->owner->id !== $this->user->id) {
      throw new MountNotOwnedException();
    } elseif($mount->hp < 30) {
      throw new MountInBadConditionException();
    } elseif($adventure->event !== null && !$adventure->event->active) {
      throw new AdventureNotAccessibleException();
    }
    $userAdventure = new UserAdventureEntity();
    $this->orm->userAdventures->attach($userAdventure);
    $userAdventure->user = $this->user->id;
    $userAdventure->adventure = $adventure;
    $userAdventure->mount = $mount;
    $this->orm->userAdventures->persistAndFlush($userAdventure);
  }
  
  /**
   * Get user's active adventure
   *
   * @throws AuthenticationNeededException
   */
  public function getCurrentAdventure(): ?UserAdventureEntity {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    } elseif($this->adventure !== null) {
      return $this->adventure;
    }
    $this->adventure = $this->orm->userAdventures->getUserActiveAdventure($this->user->id);
    return $this->adventure;
  }
  
  /**
   * Fight a npc
   *
   * @return bool Whether the user won
   */
  private function fightNpc(AdventureNpcEntity $npc, MountEntity $mount): bool {
    $combat = $this->combat;
    $combat->victoryCondition = [VictoryConditions::class, "eliminateSecondTeam"];
    $player = $this->combatHelper->getCharacter($this->user->id, $mount);
    $enemy = $this->combatHelper->getAdventureNpc($npc);
    $combat->setDuelParticipants($player, $enemy);
    $combat->execute();
    /** @var User $user */
    $user = $this->orm->users->getById($this->user->id);
    $user->life = $player->hitpoints;
    return ($combat->winner === 1);
  }

  private function saveVictory(UserAdventureEntity $adventure, AdventureNpcEntity $enemy): void {
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
    if($adventure === null) {
      throw new NotOnAdventureException();
    }
    if($adventure->progress >= UserAdventureEntity::PROGRESS_COMPLETED) {
      throw new NoEnemyRemainException();
    }
    $enemy = $adventure->nextEnemy;
    if($enemy === null) {
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
    if($adventure === null) {
      throw new NotOnAdventureException();
    }
    if($adventure->nextEnemy !== null) {
      throw new NotAllEnemiesDefeatedException();
    }
    $adventure->progress = UserAdventureEntity::PROGRESS_COMPLETED;
    $reward = $adventure->adventure->reward;
    $reward += $this->eventsModel->calculateAdventuresBonus($reward);
    $reward += $this->orderModel->calculateOrderIncomeBonus($reward);
    $adventure->user->money += $reward;
    $adventure->reward += $reward;
    $adventure->mount->hp -= MountEntity::HP_DECREASE_ADVENTURE;
    $this->orm->userAdventures->persistAndFlush($adventure);
  }
  
  /**
   * Calculate income from user's adventures from a month
   */
  public function calculateMonthAdventuresIncome(int $user = null, int $month = null, int $year = null): int {
    $income = 0;
    $adventures = $this->orm->userAdventures->findFromMonth($user ?? $this->user->id, $month, $year);
    foreach($adventures as $adventure) {
      $income += $adventure->reward + $adventure->loot;
    }
    return (int) $income;
  }
  
  /**
   * @throws AuthenticationNeededException
   */
  public function canDoAdventure(): bool {
    $twoDays = 60 * 60 * 24 * self::ADVENTURE_BREAK_DAYS_LENGTH;
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $adventure = $this->orm->userAdventures->getLastAdventure($this->user->id);
    if($adventure === null) {
      return true;
    }
    $next = new \DateTime();
    $next->setTimestamp($adventure->created + $twoDays);
    $next->setTime(0, 0, 0);
    return ($next->getTimestamp() < time());
  }
}
?>