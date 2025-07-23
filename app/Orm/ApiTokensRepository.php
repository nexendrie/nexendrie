<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method ApiToken|null getById(int $id)
 * @method ApiToken|null getBy(array $conds)
 * @method ICollection|ApiToken[] findBy(array $conds)
 * @method ICollection|ApiToken[] findAll()
 */
final class ApiTokensRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [ApiToken::class];
  }

  /**
   * @return ICollection|ApiToken[]
   */
  public function findByUser(User|int $user): ICollection {
    return $this->findBy(["user" => $user]);
  }

  public function getByToken(string $token): ?ApiToken {
    return $this->getBy(["token" => $token]);
  }

  /**
   * @return ICollection|ApiToken[]
   */
  public function findActiveForUser(User|int $user): ICollection {
    return $this->findBy(["user" => $user, "expire>=" => time(),]);
  }
}
?>