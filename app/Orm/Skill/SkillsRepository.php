<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class SkillsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Skill::class];
  }
  
  /**
   * @param int $id
   * @return Skill|null
   */
  public function getById($id): ?\Nextras\Orm\Entity\IEntity {
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