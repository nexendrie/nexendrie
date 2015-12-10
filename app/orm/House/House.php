<?php
namespace Nexendrie\Orm;

/**
 * House
 *
 * @author Jakub Konečný
 * @property User $owner {1:1 User::$house}
 * @property int $luxuryLevel {default 1}
 * @property int $breweryLevel {default 0}
 * @property int $hp {default 100}
 * @property-read int $workIncomeBonus {virtual}
 * @property-read int $upgradePrice {virtual}
 * @property-read string $upgradePriceT {virtual}
 */
class House extends \Nextras\Orm\Entity\Entity {
  const MAX_LEVEL = 5;
  const BASE_UPGRADE_PRICE = 250;
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
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
    elseif($this->owner->group->path != "city") return 0;
    else return $this->luxuryLevel * 3;
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
}
?>