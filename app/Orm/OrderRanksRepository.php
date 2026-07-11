<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<OrderRank>
 */
final class OrderRanksRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [OrderRank::class];
    }
}
