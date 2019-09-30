<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Skill as SkillEntity;
use Nexendrie\Orm\UserSkill as UserSkillEntity;
use Nextras\Orm\Collection\ICollection;

/**
 * Skills Model
 *
 * @author Jakub Konečný
 */
final class Skills {
  /** @var Events */
  protected $eventsModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
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
  public function listOfSkills(string $type = null): ICollection {
    if($type === null) {
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
    if($skill === null) {
      throw new SkillNotFoundException();
    }
    return $skill;
  }
  
  /**
   * @throws AuthenticationNeededException
   */
  public function getUserSkill(int $skill): UserSkillEntity {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $userSkill = $this->orm->userSkills->getByUserAndSkill($this->user->id, $skill);
    if($userSkill === null) {
      $userSkill = new UserSkillEntity();
      $this->orm->userSkills->attach($userSkill);
      $userSkill->skill = $skill;
      $userSkill->user = $this->user->id;
      $userSkill->level = 0;
    }
    return $userSkill;
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
    if($userSkill->level === $skill->maxLevel) {
      throw new SkillMaxLevelReachedException();
    }
    $price = $userSkill->learningPrice;
    if($userSkill->user->money < $price) {
      throw new InsufficientFundsException();
    }
    $userSkill->level++;
    $userSkill->user->money -= $price;
    $userSkill->user->lastActive = time();
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
    $level = $skill->level;
    if($level === 0) {
      $this->orm->userSkills->detach($skill);
    }
    return $level;
  }
}
?>