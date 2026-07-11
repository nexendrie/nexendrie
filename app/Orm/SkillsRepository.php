<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<Skill>
 */
final class SkillsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [Skill::class];
    }

    /**
     * @return ICollection<Skill>
     */
    public function findByType(string $type): ICollection
    {
        return $this->findBy(["type" => $type]);
    }
}
