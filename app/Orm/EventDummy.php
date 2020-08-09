<?php
declare(strict_types=1);

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
final class EventDummy extends DummyEntity {
  protected int $id;
  protected string $name;
  protected string $description;
  protected string $start;
  protected string $end;
  protected int $adventuresBonus;
  protected int $workBonus;
  protected int $prayerLifeBonus;
  protected int $trainingDiscount;
  protected int $repairingDiscount;
  protected int $shoppingDiscount;
  
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
  
  public function getId(): int {
    return $this->id;
  }
  
  public function getName(): string {
    return $this->name;
  }
  
  public function getDescription(): string {
    return $this->description;
  }
  
  public function getStart(): string {
    return $this->start;
  }
  
  public function getEnd(): string {
    return $this->end;
  }
  
  public function getAdventuresBonus(): int {
    return $this->adventuresBonus;
  }
  
  public function getWorkBonus(): int {
    return $this->workBonus;
  }
  
  public function getPrayerLifeBonus(): int {
    return $this->prayerLifeBonus;
  }
  
  public function getTrainingDiscount(): int {
    return $this->trainingDiscount;
  }
  
  public function getRepairingDiscount(): int {
    return $this->repairingDiscount;
  }
  
  public function getShoppingDiscount(): int {
    return $this->shoppingDiscount;
  }
}
?>