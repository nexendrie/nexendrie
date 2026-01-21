<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nexendrie\Utils\Numbers;
use HeroesofAbenez\Combat\Equipment;
use HeroesofAbenez\Combat\Weapon;

/**
 * UserItem
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property Item $item {m:1 Item::$userItems}
 * @property User $user {m:1 User::$items}
 * @property int $amount {default 1}
 * @property bool $worn {default false}
 * @property int $level {default 0}
 * @property int $created
 * @property int $updated
 * @property-read int $maxLevel {virtual}
 * @property-read int $upgradePrice {virtual}
 * @property-read int $price {virtual}
 * @property-read int $sellPrice {virtual}
 */
final class UserItem extends BaseEntity
{
    protected function setterAmount(int $value): int
    {
        return max(0, $value);
    }

    protected function setterLevel(int $value): int
    {
        return Numbers::clamp($value, 0, $this->maxLevel);
    }

    protected function getterMaxLevel(): int
    {
        if (!in_array($this->item->type, Item::getEquipmentTypes(), true)) {
            return 0;
        }
        return (int) max(round($this->item->strength / 2), 1);
    }

    protected function getterUpgradePrice(): int
    {
        if (!in_array($this->item->type, Item::getEquipmentTypes(), true)) {
            return 0;
        } elseif ($this->level >= $this->maxLevel) {
            return 0;
        }
        return ($this->level + 1) * (int) ($this->item->price / 3);
    }

    protected function getterPrice(): int
    {
        $price = $this->item->price;
        $i = 1;
        while ($i <= $this->level) {
            $price += $i * (int) ($this->item->price / 3);
            $i++;
        }
        return $price;
    }

    protected function getterSellPrice(): int
    {
        return (int) ($this->price / 2);
    }

    public function toCombatEquipment(): ?Equipment
    {
        if (!in_array($this->item->type, Item::getEquipmentTypes(), true)) {
            return null;
        }
        $stats = [
            "id" => $this->id, "name" => $this->item->name, "slot" => $this->item->type,
            "strength" => $this->level + $this->item->strength, "worn" => $this->worn,
        ];
        if ($stats["slot"] === Item::TYPE_WEAPON) {
            $stats["type"] = Weapon::TYPE_SWORD;
            return new Weapon($stats);
        }
        return new Equipment($stats);
    }
}
