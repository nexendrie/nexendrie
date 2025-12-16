<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Monastery|null getById(int $id)
 * @method Monastery|null getBy(array $conds)
 * @method ICollection|Monastery[] findBy(array $conds)
 * @method ICollection|Monastery[] findAll()
 */
final class MonasteriesRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [Monastery::class];
    }

    public function getByLeader(User|int $leader): ?Monastery
    {
        return $this->getBy(["leader" => $leader]);
    }

    /**
     * @return ICollection|Monastery[]
     */
    public function findByTown(Town|int $town): ICollection
    {
        return $this->findBy(["town" => $town]);
    }

    public function getByName(string $name): ?Monastery
    {
        return $this->getBy(["name" => $name]);
    }

    /**
     * Get monasteries led by users
     *
     * @return ICollection|Monastery[]
     */
    public function findLedMonasteries(): ICollection
    {
        return $this->findBy(["leader->id>" => 0]);
    }
}
