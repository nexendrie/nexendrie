<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Skill as SkillEntity,
    Nexendrie\Orm\UserSkill as UserSkillEntity,
    Nextras\Orm\Collection\ICollection;

/**
 * Skills Model
 *
 * @author Jakub Konečný
 */
class Skills {
  /** @var Events */
  protected $eventsModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** Increase of success rate per skill level (in %) */
  public const SKILL_LEVEL_SUCCESS_RATE = 5;
  /** Increase of income per skill level (in %) */
  public const SKILL_LEVEL_INCOME = 15;
  
  use \Nette\SmartObject;
  
  public function __construct(Events $eventsModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->eventsModel = $eventsModel;
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all skills
   *
   * @return SkillEntity[]|ICollection
   */
  public function listOfSkills(string $type = NULL): ICollection {
    if(is_null($type)) {
      return $this->orm->skills->findAll();
    }
    return $this->orm->skills->findByType($type);
  }
  
  /**
   * Add new skill
   */
  public function add(array $data): void {
    $skill = new SkillEntity();
    foreach($data as $key => $value) {
      $skill->$key = $value;
    }
    $this->orm->skills->persistAndFlush($skill);
  }
  
  /**
   * Edit specified skill
   *
   * @throws SkillNotFoundException
   */
  public function edit(int $id, array $data): void {
    try {
      $skill = $this->get($id);
    } catch(SkillNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      $skill->$key = $value;
    }
    $this->orm->skills->persistAndFlush($skill);
  }
  
  /**
   * Get details of specified skill
   *
   * @throws SkillNotFoundException
   */
  public function get(int $id): SkillEntity {
    $skill = $this->orm->skills->getById($id);
    if(is_null($skill)) {
      throw new SkillNotFoundException();
    }
    return $skill;
  }
  
  /**
   * @throws AuthenticationNeededException
   */
  public function getUserSkill(int $skill): ?UserSkillEntity {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    return $this->orm->userSkills->getByUserAndSkill($this->user->id, $skill);
  }
  
  /**
   * Calculate price of learning of next level
   */
  public function calculateLearningPrice(int $basePrice, int $newLevel, int $maxLevel = 5): int {
    if($newLevel === 1) {
      return ($basePrice - $this->eventsModel->calculateTrainingDiscount($basePrice));
    }
    $price = $basePrice;
    for($i = 1; $i < $newLevel; $i++) {
      $price += (int) ($basePrice / $maxLevel);
    }
    $price -= $this->eventsModel->calculateTrainingDiscount($price);
    return $price;
  }
  
  /**
   * Learn new/improve existing skill
   *
   * @throws AuthenticationNeededException
   * @throws SkillNotFoundException
   * @throws SkillMaxLevelReachedException
   * @throws InsufficientFundsException
   */
  public function learn(int $id): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    try {
      $skill = $this->get($id);
    } catch(SkillNotFoundException $e) {
      throw $e;
    }
    $userSkill = $this->getUserSkill($id);
    if(is_null($userSkill)) {
      $userSkill = new UserSkillEntity();
      $userSkill->skill = $skill;
      $userSkill->user = $this->orm->users->getById($this->user->id);
      $userSkill->level = 0;
    }
    if($userSkill->level === $skill->maxLevel) {
      throw new SkillMaxLevelReachedException();
    }
    $price = $this->calculateLearningPrice($skill->price, $userSkill->level + 1, $skill->maxLevel);
    if($userSkill->user->money < $price) {
      throw new InsufficientFundsException();
    }
    $userSkill->level++;
    $userSkill->user->money -= $price;
    $userSkill->user->lastActive = time();
    if($skill->stat === "hitpoints") {
      $userSkill->user->maxLife += $skill->statIncrease;
    }
    $this->orm->userSkills->persistAndFlush($userSkill);
  }
  
  /**
   * Get level of user's specified skill
   *
   * @throws AuthenticationNeededException
   */
  public function getLevelOfSkill(int $skillId): int {
    try {
      $skill = $this->getUserSkill($skillId);
    } catch(AuthenticationNeededException $e) {
      throw $e;
    }
    if(is_null($skill)) {
      return 0;
    }
    return $skill->level;
  }
  
  /**
   * Calculate bonus income from skill level
   *
   * @throws AuthenticationNeededException
   */
  public function calculateSkillIncomeBonus(int $baseIncome, int $skillId): int {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $bonus = 0;
    $userSkillLevel = $this->getLevelOfSkill($skillId);
    if(!is_null($userSkillLevel)) {
      $increase = $userSkillLevel * self::SKILL_LEVEL_INCOME;
      $bonus += (int) ($baseIncome / 100 * $increase);
    }
    return $bonus;
  }
  
  /**
   * Calculate bonus success rate from skill level
   *
   * @throws AuthenticationNeededException
   */
  public function calculateSkillSuccessBonus(int $skillId): int {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $bonus = 0;
    $userSkillLevel = $this->getLevelOfSkill($skillId);
    if(!is_null($userSkillLevel)) {
      $bonus += $userSkillLevel * self::SKILL_LEVEL_SUCCESS_RATE;
    }
    return $bonus;
  }
}
?>