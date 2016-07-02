<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Skill|NULL getById($id)
 * @method ICollection|Skill[] findByType($type)
 */
class SkillsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Skill::class];
  }
}
?>