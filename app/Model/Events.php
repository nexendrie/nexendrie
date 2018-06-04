<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Event;
use Nette\Utils\DateTime;
use Nette\Caching\Cache;
use Nexendrie\Orm\EventDummy;
use Nextras\Orm\Collection\ICollection;

/**
 * Events Model
 *
 * @author Jakub Konečný
 */
final class Events implements \EventCalendar\IEventModel {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Caching\Cache */
  protected $cache;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var SettingsRepository */
  protected $sr;
  /** @var \Nette\Application\LinkGenerator */
  protected $lg;
  /** @var Event[]|\Nextras\Orm\Collection\ICollection */
  private $events;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nexendrie\Orm\Model $orm, Cache $cache, \Nette\Security\User $user, SettingsRepository $sr, \Nette\Application\LinkGenerator $lg) {
    $this->orm = $orm;
    $this->cache = $cache;
    $this->user = $user;
    $this->sr = $sr;
    $this->lg = $lg;
  }
  
  /**
   * Get list of all events
   * 
   * @return Event[]|ICollection
   */
  public function listOfEvents(): ICollection {
    return $this->orm->events->findAll();
  }
  
  /**
   * Get details of specified event
   *
   * @throws EventNotFoundException
   */
  public function getEvent($id): Event {
    $event = $this->orm->events->getById($id);
    if(is_null($event)) {
      throw new EventNotFoundException();
    }
    return $event;
  }
  
  protected function getTimestamp(string $date): int {
    /** @var \Nette\Utils\DateTime $time */
    $time = DateTime::createFromFormat($this->sr->settings["locale"]["dateTimeFormat"], $date);
    return (int) $time->getTimestamp();
  }
  
  /**
   * Add new event
   */
  public function addEvent(array $data): void {
    $event = new Event();
    foreach($data as $key => $value) {
      if($key === "start" OR $key === "end") {
        $value = $this->getTimestamp($value);
      }
      $event->$key = $value;
    }
    $this->orm->events->persistAndFlush($event);
  }
  
  /**
   * Edit specified event
   *
   * @throws EventNotFoundException
   */
  public function editEvent(int $id, array $data): void {
    $event = $this->orm->events->getById($id);
    if(is_null($event)) {
      throw new EventNotFoundException();
    }
    foreach($data as $key => $value) {
      if($key === "start" OR $key === "end") {
        $value = $this->getTimestamp($value);
      }
      $event->$key = $value;
    }
    $this->orm->events->persistAndFlush($event);
  }
  
  /**
   * Delete specified events
   *
   * @throws EventNotFoundException
   * @throws CannotDeleteStartedEventException
   */
  public function deleteEvent(int $id): void {
    $event = $this->orm->events->getById($id);
    if(is_null($event)) {
      throw new EventNotFoundException();
    } elseif($event->start < time()) {
      throw new CannotDeleteStartedEventException();
    }
    $this->orm->events->removeAndFlush($event);
  }
  
  /**
   * Load events from a month
   */
  public function loadEvents(int $year = 0, int $month = 0): void {
    $this->events = $this->orm->events->findFromMonth($year, $month);
  }
  
  /**
   * @return string[]
   */
  public function getForDate(int $year, int $month, int $day): array {
    if(is_null($this->events)) {
      $this->loadEvents($year, $month);
    }
    $events = [];
    foreach($this->events as $event) {
      $startTS = mktime(0, 0, 0, $month, $day, $year);
      $date = new \DateTime;
      $date->setTimestamp($startTS);
      $date->modify("+1 day");
      $date->modify("-1 second");
      if($event->start <= $date->getTimestamp() AND $event->end >= $startTS) {
        $link = $this->lg->link("Front:Event:view", ["id" => $event->id]);
        $events[] = "<a href=\"$link\" title=\"$event->description\">$event->name</a>";
      }
    }
    return $events;
  }
  
  public function isForDate(int $year, int $month, int $day): bool {
    return (bool) count($this->getForDate($year, $month, $day));
  }
  
  /**
   * Get ongoing events
   * 
   * @return EventDummy[]
   */
  public function getCurrentEvents(): array {
    $return = $this->cache->load("events", function(&$dependencies) {
      $dependencies[Cache::EXPIRE] = "15 minutes";
      $return = [];
      $events = $this->orm->events->findForTime();
      foreach($events as $event) {
        $return[] = $event->dummy();
      }
      return $return;
    });
    return $return;
  }
  
  /**
   * Calculate current bonus for adventures
   */
  public function calculateAdventuresBonus(int $baseIncome): int {
    $bonus = 0;
    $events = $this->getCurrentEvents();
    foreach($events as $event) {
      if($event->adventuresBonus > 0) {
        $bonus += $event->adventuresBonus;
      }
    }
    return (int) ($baseIncome / 100 * $bonus);
  }
  
  /**
   * Calculate current bonus for work
   */
  public function calculateWorkBonus(int $baseIncome): int {
    $bonus = 0;
    $events = $this->getCurrentEvents();
    foreach($events as $event) {
      if($event->workBonus > 0) {
        $bonus += $event->workBonus;
      }
    }
    return (int) ($baseIncome / 100 * $bonus);
  }
  
  /**
   * Calculate current bonus for praying
   */
  public function calculatePrayerLifeBonus(int $baseValue): int {
    $bonus = 0;
    $events = $this->getCurrentEvents();
    foreach($events as $event) {
      if($event->prayerLifeBonus > 0) {
        $bonus += $event->prayerLifeBonus;
      }
    }
    return (int) ($baseValue / 100 * $bonus);
  }
  
  /**
   * Calculate current discount for training
   */
  public function calculateTrainingDiscount(int $basePrice): int {
    $discount = 0;
    $events = $this->getCurrentEvents();
    foreach($events as $event) {
      if($event->trainingDiscount > 0) {
        $discount += $event->trainingDiscount;
      }
    }
    return (int) ($basePrice / 100 * $discount);
  }
  
  /**
   * Calculate current discount for shopping
   */
  public function calculateShoppingDiscount(int $basePrice): int {
    $discount = 0;
    $events = $this->getCurrentEvents();
    foreach($events as $event) {
      if($event->shoppingDiscount > 0) {
        $discount += $event->shoppingDiscount;
      }
    }
    return (int) ($basePrice / 100 * $discount);
  }
  
  /**
   * Get current shopping discount
   */
  public function getShoppingDiscount(): int {
    $discount = 0;
    $events = $this->getCurrentEvents();
    foreach($events as $event) {
      if($event->shoppingDiscount > 0) {
        $discount += $event->shoppingDiscount;
      }
    }
    return $discount;
  }
  
  /**
   * Calculate current discount for repairing castles and monasteries
   */
  public function calculateRepairingDiscount(int $basePrice): int {
    $discount = 0;
    $events = $this->getCurrentEvents();
    foreach($events as $event) {
      if($event->repairingDiscount > 0) {
        $discount += $event->repairingDiscount;
      }
    }
    return (int) ($basePrice / 100) * $discount;
  }
}
?>