<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * EventDummy
 *
 * @author Jakub Konečný
 */
final class EventDummy extends DummyEntity {
  public readonly int $id;
  public readonly string $name;
  public readonly string $description;
  public readonly string $start;
  public readonly string $end;
  public readonly int $adventuresBonus;
  public readonly int $workBonus;
  public readonly int $prayerLifeBonus;
  public readonly int $trainingDiscount;
  public readonly int $repairingDiscount;
  public readonly int $shoppingDiscount;
  
  public function __construct(Event $event) {
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
}
?>