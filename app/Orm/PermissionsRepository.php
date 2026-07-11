<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @extends \Nextras\Orm\Repository\Repository<Permission>
 */
final class PermissionsRepository extends \Nextras\Orm\Repository\Repository
{
    public static function getEntityClassNames(): array
    {
        return [Permission::class];
    }

    /**
     * @return ICollection<Permission>
     */
    public function findByGroup(Group|int $group): ICollection
    {
        return $this->findBy(["group" => $group]);
    }
}
