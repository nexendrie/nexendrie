<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Skill as SkillEntity,
    Nexendrie\Orm\UserSkill as UserSkillEntity;

/**
 * Skills Model
 *
 * @author Jakub Konečný
 */
class Skills extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** Increase of success rate per skill level (in %) */
  const SKILL_LEVEL_SUCCESS_RATE = 5;
  /** Increase of income per skill level (in %) */
  const SKILL_LEVEL_INCOME = 15;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of all skills
   * 
   * @return SkillEntity[]
   */
  function listOfSkills() {
    return $this->orm->skills->findAll();
  }
  
  /**
   * Add new skill
   * 
   * @param array $data
   * @return void
   */
  function add(array $data) {
    $skill = new SkillEntity;
    foreach($data as $key => $value) {
      $skill->$key = $value;
    }
    $this->orm->skills->persistAndFlush($skill);
  }
  
  /**
   * Edit specified skill
   * 
   * @param int $id Skill's id
   * @param array $data
   * @return void
   */
  function edit($id, array $data) {
    $skill = $this->orm->skills->getById($id);
    foreach($data as $key => $value) {
      $skill->$key = $value;
    }
    $this->orm->skills->persistAndFlush($skill);
  }
  
  /**
   * Get details of specified skill
   * 
   * @param int $id
   * @return SkillEntity
   * @throws SkillNotFoundException
   */
  function get($id) {
    $skill = $this->orm->skills->getById($id);
    if(!$skill) throw new SkillNotFoundException;
    else return $skill;
  }
  
  /**
   * @param int $skill
   * @return UserSkillEntity|NULL
   * @throws AuthenticationNeededException
   */
  function getUserSkill($skill) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    else return $this->orm->userSkills->getByUserAndSkill($this->user->id, $skill);
  }
  
  /**
   * Calculate price of learning of next level
   * 
   * @param int $basePrice
   * @param int $newLevel
   * @return int
   */
  function calculateLearningPrice($basePrice, $newLevel) {
    if($newLevel === 1) return $basePrice;
    $price = $basePrice;
    for($i = 1; $i < $newLevel; $i++) {
      $price += (int) ($basePrice / 5);
    }
    return $price;
  }
  
  /**
   * Learn new/improve existing skill
   * 
   * @param int $id Skill's id
   * @return void
   * @throws AuthenticationNeededException
   * @throws SkillNotFoundException
   * @throws SkillMaxLevelReachedException
   * @throws InsufficientFunds
   */
  function learn($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    try {
      $skill = $this->get($id);
    } catch(SkillNotFoundException $e) {
      throw $e;
    }
    $userSkill = $this->getUserSkill($id);
    if($userSkill === NULL) {
      $userSkill = new UserSkillEntity;
      $userSkill->skill = $skill;
      $userSkill->user = $this->orm->users->getById($this->user->id);
      $userSkill->level = 0;
    }
    if($userSkill->level === 5) throw new SkillMaxLevelReachedException;
    $price = $this->calculateLearningPrice($skill->price, $userSkill->level + 1);
    if($userSkill->user->money < $price) throw new InsufficientFunds;
    $userSkill->level++;
    $userSkill->user->money -= $price;
    $this->orm->userSkills->persistAndFlush($userSkill);
  }
  
  /**
   * Get level of user's specified skill
   * 
   * @param int $skillId
   * @return int
   * @throws AuthenticationNeededException
   */
  function getLevelOfSkill($skillId) {
    try {
      $skill = $this->getUserSkill($skillId);
    } catch(AuthenticationNeededException $e) {
      throw $e;
    }
    if($skill) return $skill->level;
    else return 0;
  }
  
  /**
   * Calculate bonus income from skill level
   * 
   * @param int $baseIncome
   * @param int $skillId
   * @return int
   * @throws AuthenticationNeededException
   */
  function calculateSkillIncomeBonus($baseIncome, $skillId) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $bonus = 0;
    $userSkillLevel = $this->getLevelOfSkill($skillId);
    if($userSkillLevel) {
      $increase = $userSkillLevel * self::SKILL_LEVEL_INCOME;
      $bonus += (int) $baseIncome /100 * $increase;
    }
    return $bonus;
  }
  
  /**
   * Calculate bonus success rate from skill level
   * 
   * @param int $skillId
   * @return int
   * @throws AuthenticationNeededException
   */
  function calculateSkillSuccessBonus($skillId) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $bonus = 0;
    $userSkillLevel = $this->getLevelOfSkill($skillId);
    if($userSkillLevel) {
      $bonus += $userSkillLevel * self::SKILL_LEVEL_SUCCESS_RATE;
    }
    return $bonus;
  }
}

class SkillNotFoundException extends RecordNotFoundException {
  
}

class SkillMaxLevelReachedException extends AccessDeniedException {
  
}
?>