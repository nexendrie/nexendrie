<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Group
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $singleName
 * @property string $femaleName
 * @property int $level
 * @property string $path {enum static::PATH_*}
 * @property int $maxLoan {default 0}
 * @property int $created
 * @property int $updated
 * @property OneHasMany|User[] $members {1:m User::$group}
 * @property OneHasMany|Permission[] $permissions {1:m Permission::$group}
 */
final class Group extends BaseEntity
{
    public const PATH_CITY = "city";
    public const PATH_CHURCH = "church";
    public const PATH_TOWER = "tower";

    public function dummy(): GroupDummy
    {
        return new GroupDummy($this);
    }
}
