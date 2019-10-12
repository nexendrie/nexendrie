<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;
use Nexendrie\Utils\Numbers;

/**
 * House
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $owner {1:1 User::$house, isMain=true}
 * @property int $luxuryLevel {default 1}
 * @property int $breweryLevel {default 0}
 * @property int $hp {default 100}
 * @property int $created
 * @property OneHasMany|BeerProduction[] $beerProduction {1:m BeerProduction::$house}
 * @property-read int $workIncomeBonus {virtual}
 * @property-read int $upgradePrice {virtual}
 * @property-read string $upgradePriceT {virtual}
 * @property-read int $breweryUpgradePrice {virtual}
 * @property-read string $breweryUpgradePriceT {virtual}
 * @property-read int $repairPrice {virtual}
 * @property-read string $repairPriceT {virtual}
 */
final class House extends BaseEntity {
  public const MAX_LEVEL = 5;
  public const BASE_UPGRADE_PRICE = 250;
  public const BASE_REPAIR_PRICE = 15;
  public const INCOME_BONUS_PER_LEVEL = 3;
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  /** @var \Nexendrie\Model\Events */
  protected $eventsModel;
  /** @var int */
  protected $criticalCondition;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  public function injectEventsModel(\Nexendrie\Model\Events $eventsModel): void {
    $this->eventsModel = $eventsModel;
  }

  public function injectSettingsRepository(\Nexendrie\Model\SettingsRepository $sr): void {
    $this->criticalCondition = $sr->settings["buildings"]["criticalCondition"];
  }
  
  protected function setterLuxuryLevel(int $value): int {
    return Numbers::range($value, 1, static::MAX_LEVEL);
  }
  
  protected function setterBreweryLevel(int $value): int {
    return Numbers::range($value, 0, static::MAX_LEVEL);
  }
  
  protected function setterHp(int $value): int {
    return Numbers::range($value, 1, 100);
  }
  
  protected function getterWorkIncomeBonus(): int {
    if($this->hp < $this->criticalCondition) {
      return 0;
    } elseif($this->owner->group->path !== Group::PATH_CITY) {
      return 0;
    }
    return $this->luxuryLevel * static::INCOME_BONUS_PER_LEVEL;
  }
  
  protected function getterUpgradePrice(): int {
    if($this->luxuryLevel === static::MAX_LEVEL) {
      return 0;
    }
    $price = static::BASE_UPGRADE_PRICE;
    for($i = 2; $i < $this->luxuryLevel + 1; $i++) {
      $price += (static::BASE_UPGRADE_PRICE / static::MAX_LEVEL);
    }
    return $price;
  }
  
  protected function getterUpgradePriceT(): string {
    return $this->localeModel->money($this->upgradePrice);
  }
  
  protected function getterBreweryUpgradePrice(): int {
    if($this->breweryLevel === static::MAX_LEVEL) {
      return 0;
    }
    $price = static::BASE_UPGRADE_PRICE;
    for($i = 1; $i < $this->breweryLevel + 1; $i++) {
      $price += (static::BASE_UPGRADE_PRICE / static::MAX_LEVEL);
    }
    return $price;
  }
  
  protected function getterBreweryUpgradePriceT(): string {
    return $this->localeModel->money($this->breweryUpgradePrice);
  }
  
  protected function getterRepairPrice(): int {
    if($this->hp >= 100) {
      return 0;
    }
    $multiplier = 1;
    if($this->luxuryLevel !== 1) {
      $multiplier = ($this->luxuryLevel - 1) * 10 / 100 + 1;
    }
    $basePrice = (int) (static::BASE_REPAIR_PRICE * $multiplier * (100 - $this->hp));
    return $basePrice - $this->eventsModel->calculateRepairingDiscount($basePrice);
  }
  
  protected function getterRepairPriceT(): string {
    return $this->localeModel->money($this->repairPrice);
  }
}
?>