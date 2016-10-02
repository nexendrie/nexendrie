<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * UserSkill
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $user {m:1 User::$skills}
 * @property Skill $skill {m:1 Skill::$userSkills}
 * @property int $level
 */
class UserSkill extends \Nextras\Orm\Entity\Entity {
  
}
?>