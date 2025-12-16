<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method MountType|null getById(int $id)
 * @method MountType|null getBy(array $conds)
 * @method ICollection|MountType[] findBy(array $conds)
 * @method ICollection|MountType[] findAll()
 */
final class MountTypesRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [MountType::class];
    }
}
