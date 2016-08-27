<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class UserSkillsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [UserSkill::class];
  }
  
  /**
   * @param int $id
   * @return UserSkill|NULL
   */
  function getById($id) {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $user
   * @param Skill|int $skill
   * @return User|NULL
   */
  function getByUserAndSkill($user, $skill) {
    return $this->getBy(["user" => $user, "skill" => $skill]);
  }
  
  /**
   * @param User|int $user
   * @return ICollection|UserSkill[]
   */
  function findByUser($user) {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * 
   * @param int $user
   * @param int $stat
   * @return ICollection|UserSkill[]
   */
  function findByUserAndStat($user, $stat) {
    return $this->getBy([
      "user" => $user, "this->skill->stat" => $stat
    ]);
  }
}
?>