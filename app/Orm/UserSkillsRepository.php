<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method UserSkill|null getById(int $id)
 * @method UserSkill|null getBy(array $conds)
 * @method ICollection|UserSkill[] findBy(array $conds)
 * @method ICollection|UserSkill[] findAll()
 */
final class UserSkillsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [UserSkill::class];
  }

  public function getByUserAndSkill(User|int $user, Skill|int $skill): ?UserSkill {
    return $this->getBy(["user" => $user, "skill" => $skill]);
  }
  
  /**
   * @return ICollection|UserSkill[]
   */
  public function findByUser(User|int $user): ICollection {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * @return ICollection|UserSkill[]
   */
  public function findByUserAndStat(int $user, string $stat): ICollection {
    return $this->findBy([
      "user" => $user, "skill->stat" => $stat
    ]);
  }
}
?>