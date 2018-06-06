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
 * @property-read int $maxLevel {virtual}
 * @property-read int $upgradePrice {virtual}
 * @property-read string $upgradePriceT {virtual}
 * @property-read int $price {virtual}
 * @property-read string $priceT {virtual}
 * @property-read int $sellPrice {virtual}
 * @property-read string $sellPriceT {virtual}
 */
final class UserItem extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function setterAmount(int $value): int {
    if($value < 0) {
      return 0;
    }
    return $value;
  }
  
  protected function setterLevel(int $value): int {
    return Numbers::range($value, 0, $this->maxLevel);
  }
  
  protected function getterMaxLevel(): int {
    if(!in_array($this->item->type, Item::getEquipmentTypes(), true)) {
      return 0;
    }
    return (int) round($this->item->strength / 2) + 1;
  }
  
  protected function getterUpgradePrice(): int {
    if(!in_array($this->item->type, Item::getEquipmentTypes(), true)) {
      return 0;
    } elseif($this->level >= $this->maxLevel) {
      return 0;
    }
    return ($this->level + 1) * (int) ($this->item->price / 3);
  }
  
  protected function getterUpgradePriceT(): string {
    return $this->localeModel->money($this->upgradePrice);
  }
  
  protected function getterPrice(): int {
    $price = $this->item->price;
    $i = 1;
    while($i <= $this->level) {
      $price += $i * (int) ($this->item->price / 3);
      $i++;
    }
    return $price;
  }
  
  protected function getterPriceT(): string {
    return $this->localeModel->money($this->price);
  }
  
  protected function getterSellPrice(): int {
    return (int) ($this->price / 2);
  }
  
  protected function getterSellPriceT(): string {
    return $this->localeModel->money($this->sellPrice);
  }
  
  public function toCombatEquipment(): ?Equipment {
    if(!in_array($this->item->type, Item::getEquipmentTypes(), true)) {
      return null;
    }
    $stats = [
      "id" => $this->id, "name" => $this->item->name, "slot" => $this->item->type,
      "strength" => $this->level + $this->item->strength, "worn" => $this->worn,
    ];
    if($stats["slot"] === Item::TYPE_WEAPON) {
      $stats["type"] = Weapon::TYPE_SWORD;
      return new Weapon($stats);
    }
    return new Equipment($stats);
  }
}
?>