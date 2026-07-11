<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * ContentReportsRepository
 *
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<ContentReport>
 */
final class ContentReportsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [ContentReport::class];
    }
}
