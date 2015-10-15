<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * UserSkill
 *
 * @author Jakub Konečný
 * @property User $user {m:1 User::$skills}
 * @property Skill $skill {m:1 Skill::$userSkills}
 * @property int $level
 */
class UserSkill extends Entity {
  
}
?>