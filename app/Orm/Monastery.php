<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;
use Nexendrie\Utils\Numbers;

/**
 * Monastery
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property User $leader {m:1 User::$monasteriesLed}
 * @property Town $town {m:1 Town::$monasteries}
 * @property int $created
 * @property int $updated
 * @property int $money {default 0}
 * @property int $altairLevel {default 1}
 * @property int $libraryLevel {default 0}
 * @property int $hp {default 100}
 * @property OneHasMany|User[] $members {1:m User::$monastery, orderBy=group}
 * @property OneHasMany|MonasteryDonation[] $donations {1:m MonasteryDonation::$monastery}
 * @property OneHasMany|ChatMessage[] $chatMessages {1:m ChatMessage::$monastery}
 * @property-read string $createdAt {virtual}
 * @property-read int $prayerLife {virtual}
 * @property-read int $upgradePrice {virtual}
 * @property-read int $libraryUpgradePrice {virtual}
 * @property-read int $repairPrice {virtual}
 * @property-read int $skillLearningDiscount {virtual}
 */
final class Monastery extends BaseEntity {
  public const MAX_LEVEL = 6;
  public const BASE_UPGRADE_PRICE = 700;
  public const BASE_REPAIR_PRICE = 30;
  public const BASE_PRAYER_LIFE = 4;
  public const PRAYER_LIFE_PER_LEVEL = 2;
  public const SKILL_LEARNING_DISCOUNT_PER_LEVEL = 3;

  private \Nexendrie\Model\Locale $localeModel;
  private \Nexendrie\Model\Events $eventsModel;
  private int $criticalCondition;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  public function injectEventsModel(\Nexendrie\Model\Events $eventsModel): void {
    $this->eventsModel = $eventsModel;
  }

  public function injectSettingsRepository(\Nexendrie\Model\SettingsRepository $sr): void {
    $this->criticalCondition = $sr->settings["buildings"]["criticalCondition"];
  }
  
  protected function getterCreatedAt(): string {
    return $this->localeModel->formatDateTime($this->created);
  }
  
  protected function setterAltairLevel(int $value): int {
    return Numbers::range($value, 1, self::MAX_LEVEL);
  }

  protected function setterLibraryLevel(int $value): int {
    return Numbers::range($value, 0, self::MAX_LEVEL - 1);
  }
  
  protected function setterHp(int $value): int {
    return Numbers::range($value, 1, 100);
  }
  
  protected function getterPrayerLife(): int {
    if($this->hp < $this->criticalCondition) {
      return 0;
    }
    return self::BASE_PRAYER_LIFE + ($this->altairLevel * (self::PRAYER_LIFE_PER_LEVEL - 1));
  }
  
  protected function getterUpgradePrice(): int {
    if($this->altairLevel === self::MAX_LEVEL) {
      return 0;
    }
    $price = self::BASE_UPGRADE_PRICE;
    for($i = 2; $i < $this->altairLevel + 1; $i++) {
      $price += (int) (self::BASE_UPGRADE_PRICE / self::MAX_LEVEL);
    }
    return $price;
  }

  protected function getterLibraryUpgradePrice(): int {
    if($this->libraryLevel === self::MAX_LEVEL - 1) {
      return 0;
    }
    $price = self::BASE_UPGRADE_PRICE;
    for($i = 2; $i < $this->libraryLevel + 1; $i++) {
      $price += (self::BASE_UPGRADE_PRICE / (self::MAX_LEVEL - 1));
    }
    return $price;
  }
  
  protected function getterRepairPrice(): int {
    if($this->hp >= 100) {
      return 0;
    }
    $multiplier = 1;
    if($this->altairLevel !== 1) {
      $multiplier = ($this->altairLevel - 1) * 10 / 100 + 1;
    }
    $basePrice = (int) (self::BASE_REPAIR_PRICE * $multiplier * (100 - $this->hp));
    return $basePrice - $this->eventsModel->calculateRepairingDiscount($basePrice);
  }
  
  protected function getterSkillLearningDiscount(): int {
    if($this->hp < $this->criticalCondition) {
      return 0;
    }
    return $this->libraryLevel * self::SKILL_LEARNING_DISCOUNT_PER_LEVEL;
  }
}
?>