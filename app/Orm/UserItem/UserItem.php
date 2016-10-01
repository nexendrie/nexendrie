<?php
namespace Nexendrie\Orm;

/**
 * UserItem
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property Item $item {m:1 Item::$userItems}
 * @property User $user {m:1 User::$items}
 * @property int $amount {default 1}
 * @property bool $worn {default 0}
 * @property int $level {default 0}
 * @property-read int $maxLevel {virtual}
 * @property-read int $upgradePrice {virtual}
 * @property-read string $upgradePriceT {virtual}
 * @property-read int $price {virtual}
 * @property-read string $priceT {virtual}
 * @property-read int $sellPrice {virtual}
 * @property-read string $sellPriceT {virtual}
 */
class UserItem extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function setterAmount($value) {
    if($value < 0) return 0;
    else return $value;
  }
  
  protected function setterLevel($value) {
    if($value < 0) return 0;
    elseif($value > $this->maxLevel) return $this->maxLevel;
    else return $value;
  }
  
  protected function getterMaxLevel() {
    if(!in_array($this->item->type, Item::getEquipmentTypes())) return 0;
    else return (int) round($this->item->strength / 2) + 1;
  }
  
  protected function getterUpgradePrice() {
    if(!in_array($this->item->type, Item::getEquipmentTypes())) return 0;
    elseif($this->level >= $this->maxLevel) return 0;
    else return ($this->level + 1) * (int) ($this->item->price / 3);
  }
  
  protected function getterUpgradePriceT() {
    return $this->localeModel->money($this->upgradePrice);
  }
  
  protected function getterPrice() {
    $price = $this->item->price;
    $i = 1;
    while($i <= $this->level) {
      $price += $i * (int) ($this->item->price / 3);
      $i++;
    }
    return $price;
  }
  
  protected function getterPriceT() {
    return $this->localeModel->money($this->price);
  }
  
  protected function getterSellPrice() {
    return (int) ($this->price / 2);
  }
  
  protected function getterSellPriceT() {
    return $this->localeModel->money($this->sellPrice);
  }
}
?>