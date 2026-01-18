<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Item
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $description
 * @property int $price
 * @property Shop|null $shop {m:1 Shop::$items}
 * @property string $type {enum static::TYPE_*} {default static::TYPE_ITEM}
 * @property int $strength {default 0}
 * @property int $created
 * @property int $updated
 * @property OneHasMany|UserItem[] $userItems {1:m UserItem::$item}
 * @property-read string $typeCZ {virtual}
 * @property OneHasMany|ItemSet[] $weaponSets {1:m ItemSet::$weapon}
 * @property OneHasMany|ItemSet[] $armorSets {1:m ItemSet::$armor}
 * @property OneHasMany|ItemSet[] $helmetSets {1:m ItemSet::$helmet}
 */
final class Item extends BaseEntity
{
    public const string TYPE_ITEM = "item";
    public const string TYPE_WEAPON = "weapon";
    public const string TYPE_ARMOR = "armor";
    public const string TYPE_HELMET = "helmet";
    public const string TYPE_AMULET = "amulet";
    public const string TYPE_POTION = "potion";
    public const string TYPE_MATERIAL = "material";
    public const string TYPE_CHARTER = "charter";
    public const string TYPE_INTIMACY_BOOST = "intimacy_boost";

    /**
     * @return array<string, string>
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_ITEM => "Věc",
            self::TYPE_WEAPON => "Zbraň",
            self::TYPE_ARMOR => "Brnění",
            self::TYPE_HELMET => "Helma",
            self::TYPE_AMULET => "Amulet",
            self::TYPE_POTION => "Lektvar",
            self::TYPE_MATERIAL => "Surovina",
            self::TYPE_CHARTER => "Listina",
            self::TYPE_INTIMACY_BOOST => "Zvýšení důvěrnosti",
        ];
    }

    /**
     * @return string[]
     */
    public static function getCommonTypes(): array
    {
        return [
            self::TYPE_ITEM, self::TYPE_MATERIAL, self::TYPE_CHARTER, self::TYPE_INTIMACY_BOOST
        ];
    }

    /**
     * @return string[]
     */
    public static function getEquipmentTypes(): array
    {
        return [
            self::TYPE_WEAPON, self::TYPE_ARMOR, self::TYPE_HELMET, self::TYPE_AMULET,
        ];
    }

    /**
     * @return string[]
     */
    public static function getNotForSale(): array
    {
        return [
            self::TYPE_CHARTER, self::TYPE_INTIMACY_BOOST
        ];
    }

    protected function getterTypeCZ(): string
    {
        return self::getTypes()[$this->type];
    }
}
