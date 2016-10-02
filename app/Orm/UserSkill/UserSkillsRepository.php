<?php
declare(strict_types=1);

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
  function findByUser($user): ICollection {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * 
   * @param int $user
   * @param string $stat
   * @return ICollection|UserSkill[]
   */
  function findByUserAndStat(int $user, string $stat): ICollection {
    return $this->findBy([
      "user" => $user, "this->skill->stat" => $stat
    ]);
  }
}
?>