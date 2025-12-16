<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method ElectionResult|null getById(int $id)
 * @method ElectionResult|null getBy(array $conds)
 * @method ICollection|ElectionResult[] findBy(array $conds)
 * @method ICollection|ElectionResult[] findAll()
 */
final class ElectionResultsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [ElectionResult::class];
    }

    /**
     * @return ICollection|ElectionResult[]
     */
    public function findByTownAndYearAndMonth(Town|int $town, int $year, int $month): ICollection
    {
        return $this->findBy(["town" => $town, "year" => $year, "month" => $month]);
    }
}
