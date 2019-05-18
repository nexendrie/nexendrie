<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class JobsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Job::class];
  }
  
  /**
   * @param int $id
   * @return Job|null
   */
  public function getById($id): ?\Nextras\Orm\Entity\IEntity {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param Skill|int $skill
   * @return ICollection|Job[]
   */
  public function findBySkill($skill): ICollection {
    return $this->findBy(["neededSkill" => $skill]);
  }
  
  /**
   * Find jobs for specified level
   *
   * @return ICollection|Job[]
   */
  public function findForLevel(int $level): ICollection {
    return $this->findBy(["level<=" => $level]);
  }
}
?>