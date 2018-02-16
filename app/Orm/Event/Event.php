<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Event
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $description
 * @property int $start
 * @property-read string $startAt {virtual}
 * @property int $end
 * @property-read string $endAt {virtual}
 * @property int $adventuresBonus {default 0}
 * @property int $workBonus {default 0}
 * @property int $prayerLifeBonus {default 0}
 * @property int $trainingDiscount {default 0}
 * @property int $repairingDiscount {default 0}
 * @property int $shoppingDiscount {default 0}
 * @property OneHasMany|Adventure[] $adventures {1:m Adventure::$event}
 * @property-read bool $active {virtual}
 */
class Event extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterStartAt(): string {
    return $this->localeModel->formatDateTime($this->start);
  }
  
  protected function setterEnd(int $value): int {
    if($value < $this->start) {
      return $this->start;
    }
    return $value;
  }
  
  protected function getterEndAt(): string {
    return $this->localeModel->formatDateTime($this->end);
  }
  
  protected function setterAdventuresBonus(int $value): int {
    if($value < 0) {
      return 0;
    } elseif($value > 999) {
      return 999;
    }
    return $value;
  }
  
  protected function setterWorkBonus(int $value): int {
    if($value < 0) {
      return 0;
    } elseif($value > 999) {
      return 999;
    }
    return $value;
  }
  
  protected function setterPrayerLifeBonus(int $value): int {
    if($value < 0) {
      return 0;
    } elseif($value > 999) {
      return 999;
    }
    return $value;
  }
  
  protected function setterTrainingDiscount(int $value): int {
    if($value < 0) {
      return 0;
    } elseif($value > 100) {
      return 100;
    }
    return $value;
  }
  
  protected function setterRepairingDiscount(int $value): int {
    if($value < 0) {
      return 0;
    } elseif($value > 100) {
      return 100;
    }
    return $value;
  }
  
  protected function setterShoppingDiscount(int $value): int {
    if($value < 0) {
      return 0;
    } elseif($value > 100) {
      return 100;
    }
    return $value;
  }
  
  protected function getterActive(): bool {
    $time = time();
    return ($this->start <= $time AND $this->end >= $time);
  }
  
  public function dummy(): EventDummy {
    return new EventDummy($this);
  }
  
  public function dummyArray(): array {
    return $this->dummy()->toArray();
  }
}
?>