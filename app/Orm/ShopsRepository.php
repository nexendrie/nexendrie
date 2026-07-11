<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<Shop>
 */
final class ShopsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [Shop::class];
    }
}
