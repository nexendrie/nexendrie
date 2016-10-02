<?php
declare(strict_types=1);

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
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param string $type
   * @return ICollection|Skill[]
   */
  function findByType($type) {
    return $this->findBy(["type" => $type]);
  }
}
?>