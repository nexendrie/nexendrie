<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<ApiToken>
 */
final class ApiTokensRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [ApiToken::class];
    }

    /**
     * @return ICollection<ApiToken>
     */
    public function findByUser(User|int $user): ICollection
    {
        return $this->findBy(["user" => $user]);
    }

    public function getByToken(string $token): ?ApiToken
    {
        return $this->getBy(["token" => $token]);
    }

    /**
     * @return ICollection<ApiToken>
     */
    public function findActiveForUser(User|int $user): ICollection
    {
        return $this->findBy(["user" => $user, "expire>=" => time(),]);
    }
}
