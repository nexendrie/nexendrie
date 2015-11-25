<?php
namespace Nexendrie\Orm;

/**
 * UserItem
 *
 * @author Jakub Konečný
 * @property Item $item {m:1 Item}
 * @property User $user {m:1 User::$items}
 * @property int $amount {default 1}
 * @property bool $worn {default 0}
 * @property int $level {default 0}
 * @property-read int $maxLevel {virtual}
 * @property-read int $upgradePrice {virtual}
 * @property-read string $upgradePriceT {virtual}
 */
class UserItem extends \Nextras\Orm\Entity\Entity {
  const UPGRADE_PRICE = 25;
  
  /** @var \Nexendrie\Model\Locale $localeModel */
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
    elseif($this->item->strength < 4) return 2;
    else return 3;
  }
  
  protected function getterUpgradePrice() {
    if(!in_array($this->item->type, Item::getEquipmentTypes())) return 0;
    elseif($this->level >= $this->maxLevel) return 0;
    else return ($this->level + 1) * self::UPGRADE_PRICE;
  }
  
  protected function getterUpgradePriceT() {
    return $this->localeModel->money($this->upgradePrice);
  }
}
?>