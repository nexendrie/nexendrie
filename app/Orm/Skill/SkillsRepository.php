<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class SkillsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames(): array {
    return [Skill::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Skill {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param string $type
   * @return ICollection|Skill[]
   */
  public function findByType($type): ICollection {
    return $this->findBy(["type" => $type]);
  }
}
?>