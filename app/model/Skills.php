<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Skill as SkillEntity;

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
   * Learn new/improve existing skill
   * 
   * @param int $id Skill's id
   * @return void
   */
  function learn($id) {
    
  }
}

class SkillNotFoundException extends RecordNotFoundException {
  
}
?>