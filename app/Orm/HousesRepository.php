<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<House>
 */
final class HousesRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [House::class];
    }

    public function getByOwner(User|int $owner): ?House
    {
        return $this->getBy(["owner" => $owner]);
    }

    /**
     * Get houses owned by users
     *
     * @return ICollection<House>
     */
    public function findOwnedHouses(): ICollection
    {
        return $this->findBy(["owner->id>" => 0]);
    }
}
