<?php
namespace Nexendrie\Orm;

/**
 * UserSkill
 *
 * @author Jakub Konečný
 * @property User $user {m:1 User::$skills} {primary}
 * @property Skill $skill {m:1 Skill::$userSkills} {primary}
 * @property int $level
 */
class UserSkill extends \Nextras\Orm\Entity\Entity {
  
}
?>