<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * House
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $owner {1:1 User::$house, isMain=true}
 * @property int $luxuryLevel {default 1}
 * @property int $breweryLevel {default 0}
 * @property int $hp {default 100}
 * @property OneHasMany|BeerProduction[] $beerProduction {1:m BeerProduction::$house}
 * @property-read int $workIncomeBonus {virtual}
 * @property-read int $upgradePrice {virtual}
 * @property-read string $upgradePriceT {virtual}
 * @property-read int $breweryUpgradePrice {virtual}
 * @property-read string $breweryUpgradePriceT {virtual}
 * @property-read int $repairPrice {virtual}
 * @property-read string $repairPriceT {virtual}
 */
class House extends \Nextras\Orm\Entity\Entity {
  const MAX_LEVEL = 5;
  const BASE_UPGRADE_PRICE = 250;
  const BASE_REPAIR_PRICE = 15;
  const INCOME_BONUS_PER_LEVEL = 3;
  
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
  
  protected function setterLuxuryLevel($value) {
    if($value < 1) return 1;
    elseif($value > self::MAX_LEVEL) return self::MAX_LEVEL;
    else return $value;
  }
  
  protected function setterBreweryLevel($value) {
    if($value < 0) return 0;
    elseif($value > self::MAX_LEVEL) return self::MAX_LEVEL;
    else return $value;
  }
  
  protected function setterHp($value) {
    if($value < 1) return 1;
    elseif($value > 100) return 100;
    else return $value;
  }
  
  protected function getterWorkIncomeBonus() {
    if($this->hp <= 30) return 0;
    elseif($this->owner->group->path != Group::PATH_CITY) return 0;
    else return $this->luxuryLevel * self::INCOME_BONUS_PER_LEVEL;
  }
  
  protected function getterUpgradePrice() {
    if($this->luxuryLevel === self::MAX_LEVEL) return 0;
    $price = self::BASE_UPGRADE_PRICE;
    for($i = 2; $i < $this->luxuryLevel + 1; $i++) {
      $price += (int) (self::BASE_UPGRADE_PRICE / self::MAX_LEVEL);
    }
    return $price;
  }
  
  protected function getterUpgradePriceT() {
    return $this->localeModel->money($this->upgradePrice);
  }
  
  protected function getterBreweryUpgradePrice() {
    if($this->breweryLevel === self::MAX_LEVEL) return 0;
    $price = self::BASE_UPGRADE_PRICE;
    for($i = 1; $i < $this->breweryLevel + 1; $i++) {
      $price += (int) (self::BASE_UPGRADE_PRICE / self::MAX_LEVEL);
    }
    return $price;
  }
  
  protected function getterBreweryUpgradePriceT() {
    return $this->localeModel->money($this->breweryUpgradePrice);
  }
  
  protected function getterRepairPrice() {
    if($this->hp >= 100) return 0;
    if($this->luxuryLevel === 1) $multiplier = 1; else $multiplier = ($this->level - 1) * 10 / 100 + 1;
    $basePrice = self::BASE_REPAIR_PRICE * $multiplier * (100 - $this->hp);
    return (int) ($basePrice - $this->eventsModel->calculateRepairingDiscount($basePrice));
  }
  
  protected function getterRepairPriceT() {
    return $this->localeModel->money($this->repairPrice);
  }
}
?>