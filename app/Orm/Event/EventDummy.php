<?php
namespace Nexendrie\Orm;

/**
 * EventDummy
 *
 * @author Jakub Konečný
 * @property-read int $id
 * @property-read string $name
 * @property-read string $description
 * @property-read string $start
 * @property-read string $end
 * @property-read int $adventuresBonus
 * @property-read int $workBonus
 * @property-read int $prayerLifeBonus
 * @property-read int $trainingDiscount
 * @property-read int $repairingDiscount
 * @property-read int $shoppingDiscount
 */
class EventDummy extends DummyEntity {
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
    $this->shoppingDiscount = $event->shoppingDiscount;
  }
  
  function getId() {
    return $this->id;
  }
  
  function getName() {
    return $this->name;
  }
  
  function getDescription() {
    return $this->description;
  }
  
  function getStart() {
    return $this->start;
  }
  
  function getEnd() {
    return $this->end;
  }
  
  function getAdventuresBonus() {
    return $this->adventuresBonus;
  }
  
  function getWorkBonus() {
    return $this->workBonus;
  }
  
  function getPrayerLifeBonus() {
    return $this->prayerLifeBonus;
  }
  
  function getTrainingDiscount() {
    return $this->trainingDiscount;
  }
  
  function getRepairingDiscount() {
    return $this->repairingDiscount;
  }
  
  function getCaringDiscount() {
    return $this->caringDiscount;
  }
  
  function getShoppingDiscount() {
    return $this->shoppingDiscount;
  }
}
?>