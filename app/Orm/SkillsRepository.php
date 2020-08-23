<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Skill|null getById(int $id)
 * @method Skill|null getBy(array $conds)
 * @method ICollection|Skill[] findBy(array $conds)
 * @method ICollection|Skill[] findAll()
 */
final class SkillsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Skill::class];
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