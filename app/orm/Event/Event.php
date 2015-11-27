<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Event
 *
 * @author Jakub Konečný
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
 */
class Event extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterStartAt() {
    return $this->localeModel->formatDateTime($this->start);
  }
  
  protected function setterEnd($value) {
    if($value < $this->start) return $this->start;
    else return $value;
  }
  
  protected function getterEndAt() {
    return $this->localeModel->formatDateTime($this->end);
  }
  
  protected function setterAdventuresBonus($value) {
    if($value < 0) return 0;
    elseif($value > 999) return 999;
    else return $value;
  }
  
  protected function setterWorkBonus($value) {
    if($value < 0) return 0;
    elseif($value > 999) return 999;
    else return $value;
  }
  
  protected function setterPrayerLifeBonus($value) {
    if($value < 0) return 0;
    elseif($value > 999) return 999;
    else return $value;
  }
  
  protected function setterTrainingDiscount($value) {
    if($value < 0) return 0;
    elseif($value > 100) return 100;
    else return $value;
  }
  
  protected function setterRepairingDiscount($value) {
    if($value < 0) return 0;
    elseif($value > 100) return 100;
    else return $value;
  }
  
  protected function setterShoppingDiscount($value) {
    if($value < 0) return 0;
    elseif($value > 100) return 100;
    else return $value;
  }
  
  /**
   * @return EventDummy
   */
  function dummy() {
    return new EventDummy($this);
  }
  
  /**
   * @return array
   */
  function dummyArray() {
    return $this->dummy()->toArray();
  }
}
?>