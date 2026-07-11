<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<Job>
 */
final class JobsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [Job::class];
    }

    /**
     * @return ICollection<Job>
     */
    public function findBySkill(Skill|int $skill): ICollection
    {
        return $this->findBy(["neededSkill" => $skill]);
    }

    /**
     * Find jobs for specified level
     *
     * @return ICollection<Job>
     */
    public function findForLevel(int $level): ICollection
    {
        return $this->findBy(["level<=" => $level]);
    }
}
