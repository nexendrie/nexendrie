<?php
namespace Nexendrie\Orm;

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