<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<Punishment>
 */
final class PunishmentsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [Punishment::class];
    }

    /**
     * @return ICollection<Punishment>
     */
    public function findByUser(User|int $user): ICollection
    {
        return $this->findBy(["user" => $user]);
    }

    /**
     * @return ICollection<Punishment>
     */
    public function findByUserPublicname(string $username): ICollection
    {
        return $this->findBy(["user->publicname" => $username]);
    }

    /**
     * Find specified user's active punishment
     */
    public function getActivePunishment(int $user): ?Punishment
    {
        return $this->getBy(["user" => $user, "released" => null]);
    }
}
