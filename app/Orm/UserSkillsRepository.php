<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class UserSkillsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [UserSkill::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?UserSkill {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param User|int $user
   * @param Skill|int $skill
   */
  public function getByUserAndSkill($user, $skill): ?UserSkill {
    return $this->getBy(["user" => $user, "skill" => $skill]);
  }
  
  /**
   * @param User|int $user
   * @return ICollection|UserSkill[]
   */
  public function findByUser($user): ICollection {
    return $this->findBy(["user" => $user]);
  }
  
  /**
   * @return ICollection|UserSkill[]
   */
  public function findByUserAndStat(int $user, string $stat): ICollection {
    return $this->findBy([
      "user" => $user, "this->skill->stat" => $stat
    ]);
  }
}
?>