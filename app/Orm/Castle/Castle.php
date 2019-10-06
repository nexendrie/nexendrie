<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nexendrie\Utils\Numbers;

/**
 * Castle
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $description
 * @property int $created
 * @property-read string $createdAt {virtual}
 * @property User $owner {1:1 User::$castle, isMain=true}
 * @property int $level {default 1}
 * @property int $hp {default 100}
 * @property-read int $taxesBonusIncome {virtual}
 * @property-read int $upgradePrice {virtual}
 * @property-read string $upgradePriceT {virtual}
 * @property-read int $repairPrice {virtual}
 * @property-read string $repairPriceT {virtual}
 */
final class Castle extends \Nextras\Orm\Entity\Entity {
  public const MAX_LEVEL = 5;
  public const BASE_UPGRADE_PRICE = 500;
  public const BASE_REPAIR_PRICE = 35;
  public const TAX_BONUS_PER_LEVEL = 30;
  
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
  
  protected function setterLevel(int $value): int {
    return Numbers::range($value, 1, static::MAX_LEVEL);
  }
  
  protected function setterHp(int $value): int {
    return Numbers::range($value, 1, 100);
  }
  
  protected function getterCreatedAt(): string {
    return $this->localeModel->formatDate($this->created);
  }
  
  protected function getterTaxesBonusIncome(): int {
    if($this->hp < $this->criticalCondition) {
      return 0;
    } elseif($this->owner->group->path !== Group::PATH_TOWER) {
      return 0;
    }
    return $this->level * static::TAX_BONUS_PER_LEVEL;
  }
  
  protected function getterUpgradePrice(): int {
    if($this->level === static::MAX_LEVEL) {
      return 0;
    }
    $price = static::BASE_UPGRADE_PRICE;
    for($i = 2; $i < $this->level + 1; $i++) {
      $price += (static::BASE_UPGRADE_PRICE / static::MAX_LEVEL);
    }
    return $price;
  }
  
  protected function getterUpgradePriceT(): string {
    return $this->localeModel->money($this->upgradePrice);
  }
  
  protected function getterRepairPrice(): int {
    if($this->hp >= 100) {
      return 0;
    }
    $multiplier = 1;
    if($this->level !== 1) {
      $multiplier = ($this->level - 1) * 10 / 100 + 1;
    }
    $basePrice = (int) (static::BASE_REPAIR_PRICE * $multiplier * (100 - $this->hp));
    return $basePrice - $this->eventsModel->calculateRepairingDiscount($basePrice);
  }
  
  protected function getterRepairPriceT(): string {
    return $this->localeModel->money($this->repairPrice);
  }
  
  public function onBeforeInsert(): void {
    parent::onBeforeInsert();
    $this->created = time();
  }
}
?>