<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method UserSkill|NULL getById($id)
 * @method UserSkill|NULL getByUserAndSkill($user,$skill)
 * @method ICollection|UserSkill findByUser($user)
 */
class UserSkillsRepository extends \Nextras\Orm\Repository\Repository {
  
}
?>