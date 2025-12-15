<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Invitation|null getById(int $id)
 * @method Invitation|null getBy(array $conds)
 * @method ICollection|Invitation[] findBy(array $conds)
 * @method ICollection|Invitation[] findAll()
 */
final class InvitationsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Invitation::class];
  }

  public function getByEmail(string $email): ?Invitation {
    return $this->getBy(["email" => $email,]);
  }
  
  /**
   * @return ICollection|Invitation[]
   */
  public function findByInviter(User|int $user): ICollection {
    return $this->findBy(["inviter" => $user]);
  }
  
  /**
   * @return ICollection|Invitation[]
   */
  public function findByUserAndStat(int $user, string $stat): ICollection {
    return $this->findBy([
      "user" => $user, "skill->stat" => $stat
    ]);
  }
}
?>