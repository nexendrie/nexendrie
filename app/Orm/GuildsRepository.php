<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<Guild>
 */
final class GuildsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [Guild::class];
    }

    public function getByName(string $name): ?Guild
    {
        return $this->getBy(["name" => $name]);
    }

    /**
     * @return ICollection<Guild>
     */
    public function findByTown(Town|int $town): ICollection
    {
        return $this->findBy(["town" => $town]);
    }

    /**
     * @return ICollection<Guild>
     */
    public function findBySkill(Skill|int $skill): ICollection
    {
        return $this->findBy(["skill" => $skill]);
    }
}
