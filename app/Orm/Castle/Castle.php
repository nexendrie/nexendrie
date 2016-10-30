<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * Castle
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $description
 * @property int $founded
 * @property-read string $foundedAt {virtual}
 * @property User $owner {1:1 User::$castle, isMain=true}
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
  
  protected function setterLevel(int $value): int {
    if($value < 1) return 1;
    elseif($value > self::MAX_LEVEL) return self::MAX_LEVEL;
    else return $value;
  }
  
  protected function setterHp(int $value): int {
    if($value < 1) return 1;
    elseif($value > 100) return 100;
    else return $value;
  }
  
  protected function getterFoundedAt(): string {
    return $this->localeModel->formatDate($this->founded);
  }
  
  protected function getterTaxesBonusIncome(): int {
    if($this->hp <= 30) return 0;
    elseif($this->owner->group->path != Group::PATH_TOWER) return 0;
    else return $this->level * self::TAX_BONUS_PER_LEVEL;
  }
  
  protected function getterUpgradePrice(): int {
    if($this->level === self::MAX_LEVEL) return 0;
    $price = self::BASE_UPGRADE_PRICE;
    for($i = 2; $i < $this->level + 1; $i++) {
      $price += (int) (self::BASE_UPGRADE_PRICE / self::MAX_LEVEL);
    }
    return $price;
  }
  
  protected function getterUpgradePriceT(): string {
    return $this->localeModel->money($this->upgradePrice);
  }
  
  protected function getterRepairPrice(): int {
    if($this->hp >= 100) return 0;
    if($this->level === 1) $multiplier = 1;
    else $multiplier = ($this->level - 1) * 10 / 100 + 1;
    $basePrice = (int) (self::BASE_REPAIR_PRICE * $multiplier * (100 - $this->hp));
    return (int) ($basePrice - $this->eventsModel->calculateRepairingDiscount($basePrice));
  }
  
  protected function getterRepairPriceT(): string {
    return $this->localeModel->money($this->repairPrice);
  }
  
  protected function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->founded = time();
  }
}
?>