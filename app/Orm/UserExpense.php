<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * UserExpense
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $category {enum self::CATEGORY_*}
 * @property int $amount
 * @property User $user {m:1 User::$expenses}
 * @property int $created
 * @property-read AdventureNpc|null $nextEnemy {virtual}
 */
final class UserExpense extends BaseEntity
{
    public const CATEGORY_CASTLE_MAINTENANCE = "castle_maintenance";
    public const CATEGORY_HOUSE_MAINTENANCE = "house_maintenance";
    public const CATEGORY_MOUNT_MAINTENANCE = "mount_maintenance";
}
