<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<Castle>
 */
final class CastlesRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [Castle::class];
    }

    public function getByOwner(User|int $owner): ?Castle
    {
        return $this->getBy(["owner" => $owner]);
    }

    public function getByName(string $name): ?Castle
    {
        return $this->getBy(["name" => $name]);
    }

    /**
     * Get castles owned by users
     *
     * @return ICollection<Castle>
     */
    public function findOwnedCastles(): ICollection
    {
        return $this->findBy(["owner->id>" => 0]);
    }
}
