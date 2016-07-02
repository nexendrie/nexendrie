<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method UserSkill|NULL getById($id)
 * @method UserSkill|NULL getByUserAndSkill($user,$skill)
 * @method ICollection|UserSkill[] findByUser($user)
 */
class UserSkillsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [UserSkill::class];
  }
  
  /**
   * 
   * @param int $user
   * @param int $stat
   * @return ICollection|UserSkill[]
   */
  function findByUserAndStat($user, $stat) {
    return $this->getBy(array(
      "user" => $user, "this->skill->stat" => $stat
    ));
  }
}
?>