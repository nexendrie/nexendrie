<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class SkillsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Skill::class];
  }
  
  /**
   * @param int $id
   * @return Skill|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * @param string $type
   * @return ICollection|Skill[]
   */
  function findByType($type) {
    return $this->findBy(array("type" => $type));
  }
}
?>