<?php
namespace Nexendrie\Orm;

/**
 * EventDummy
 *
 * @author Jakub Konečný
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $start
 * @property string $end
 * @property int $adventuresBonus
 * @property int $workBonus
 * @property int $prayerLifeBonus
 * @property int $trainingDiscount
 * @property int $repairingDiscount
 * @property int $caringDiscount
 * @property int $shoppingDiscount
 */
class EventDummy extends \Nette\Object {
  /** @var int */
  protected $id;
  /** @var string */
  protected $name;
  /** @var string */
  protected $description;
  /** @var string */
  protected $start;
  /** @var string */
  protected $end;
  /** @var int */
  protected $adventuresBonus;
  /** @var int */
  protected $workBonus;
  /** @var int */
  protected $prayerLifeBonus;
  /** @var int */
  protected $trainingDiscount;
  /** @var int */
  protected $repairingDiscount;
  /** @var int */
  protected $caringDiscount;
  /** @var int */
  protected $shoppingDiscount;
  
  function __construct(Event $event) {
    $this->id = $event->id;
    $this->name = $event->name;
    $this->description = $event->description;
    $this->start = $event->startAt;
    $this->end = $event->endAt;
    $this->adventuresBonus = $event->adventuresBonus;
    $this->workBonus = $event->workBonus;
    $this->prayerLifeBonus = $event->prayerLifeBonus;
    $this->trainingDiscount = $event->trainingDiscount;
    $this->repairingDiscount = $event->repairingDiscount;
    $this->caringDiscount = $event->caringDiscount;
    $this->shoppingDiscount = $event->shoppingDiscount;
  }
  
  /**
   * @return array
   */
  function toArray() {
    $return = array();
    foreach($this as $key => $value) {
      $return[$key] = $value;
    }
    return $return;
  }
}
?>