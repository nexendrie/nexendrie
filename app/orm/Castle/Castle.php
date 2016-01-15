<?php
namespace Nexendrie\Orm;

/**
 * Castle
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property int $founded
 * @property-read string $foundedAt {virtual}
 * @property User $owner {1:1 User::$castle}
 * @property int $level {default 1}
 * @property int $hp {default 100}
 * @property-read int $taxesBonusIncome {virtual}
 * @property-read int $upgradePrice {virtual}
 * @property-read string $upgradePriceT {virtual}
 * @property-read int $repairPrice {virtual}
 * @property-read string $repairPriceT {virtual}
 */
class Castle extends \Nextras\Orm\Entity\Entity {
  const MAX_LEVEL = 5;
  const BASE_UPGRADE_PRICE = 500;
  const BASE_REPAIR_PRICE = 35;
  const TAX_BONUS_PER_LEVEL = 30;
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  /** @var \Nexendrie\Model\Events */
  protected $eventsModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  function injectEventsModel(\Nexendrie\Model\Events $eventsModel) {
    $this->eventsModel = $eventsModel;
  }
  
  protected function setterLevel($value) {
    if($value < 1) return 1;
    elseif($value > self::MAX_LEVEL) return self::MAX_LEVEL;
    else return $value;
  }
  
   protected function setterHp($value) {
    if($value < 1) return 1;
    elseif($value > 100) return 100;
    else return $value;
  }
  
  protected function getterFoundedAt() {
    return $this->localeModel->formatDate($this->founded);
  }
  
  protected function getterTaxesBonusIncome() {
    if($this->hp <= 30) return 0;
    elseif($this->owner->group->path != "tower") return 0;
    else return $this->level * self::TAX_BONUS_PER_LEVEL;
  }
  
  protected function getterUpgradePrice() {
    if($this->level === self::MAX_LEVEL) return 0;
    $price = self::BASE_UPGRADE_PRICE;
    for($i = 2; $i < $this->level + 1; $i++) {
      $price += (int) (self::BASE_UPGRADE_PRICE / self::MAX_LEVEL);
    }
    return $price;
  }
  
  protected function getterUpgradePriceT() {
    return $this->localeModel->money($this->upgradePrice);
  }
  
  protected function getterRepairPrice() {
    if($this->hp >= 100) return 0;
    $basePrice = self::BASE_REPAIR_PRICE * (100 - $this->hp);
    return (int) ($basePrice - $this->eventsModel->calculateRepairingDiscount($basePrice));
  }
  
  protected function getterRepairPriceT() {
    return $this->localeModel->money($this->repairPrice);
  }
}
?>