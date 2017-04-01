<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Event,
    Nette\Utils\DateTime,
    Nette\Caching\Cache,
    Nexendrie\Orm\EventDummy,
    Nextras\Orm\Collection\ICollection;

/**
 * Events Model
 *
 * @author Jakub Konečný
 */
class Events implements \EventCalendar\IEventModel {
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
  
  function __construct(\Nexendrie\Orm\Model $orm, Cache $cache, \Nette\Security\User $user, SettingsRepository $sr, \Nette\Application\LinkGenerator $lg) {
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
  function listOfEvents(): ICollection {
    return $this->orm->events->findAll();
  }
  
  /**
   * Get details of specified event
   * 
   * @param int $id
   * @return Event
   * @throws EventNotFoundException
   */
  function getEvent($id): Event {
    $event = $this->orm->events->getById($id);
    if(!$event) {
      throw new EventNotFoundException;
    }
    else {
      return $event;
    }
  }
  
  /**
   * Add new event
   * 
   * @param array $data
   * @return void
   */
  function addEvent(array $data): void {
    $event = new Event;
    foreach($data as $key => $value) {
      if($key === "start" OR $key === "end") {
        $time = DateTime::createFromFormat($this->sr->settings["locale"]["dateTimeFormat"], $value);
        $value = $time->getTimestamp();
      }
      $event->$key = $value;
    }
    $this->orm->events->persistAndFlush($event);
  }
  
  /**
   * Edit specified event
   * 
   * @param int $id
   * @param array $data
   * @return void
   * @throws EventNotFoundException
   */
  function editEvent(int $id, array $data): void {
    $event = $this->orm->events->getById($id);
    if(!$event) {
      throw new EventNotFoundException;
    }
    foreach($data as $key => $value) {
      if($key === "start" OR $key === "end") {
        $time = DateTime::createFromFormat($this->sr->settings["locale"]["dateTimeFormat"], $value);
        $value = $time->getTimestamp();
      }
      $event->$key = $value;
    }
    $this->orm->events->persistAndFlush($event);
  }
  
  /**
   * Delete specified events
   * 
   * @param int $id
   * @return void
   * @throws EventNotFoundException
   * @throws CannotDeleteStartedEventException
   */
  function deleteEvent(int $id): void {
    $event = $this->orm->events->getById($id);
    if(!$event) {
      throw new EventNotFoundException;
    }
    elseif($event->start < time()) {
      throw new CannotDeleteStartedEventException;
    }
    else {
      $this->orm->events->removeAndFlush($event);
    }
  }
  
  /**
   * Load events from a month
   * 
   * @param int $year
   * @param int $month
   * @return void
   */
  function loadEvents(int $year = 0, int $month = 0): void {
    $this->events = $this->orm->events->findFromMonth($year, $month);
  }
  
  /**
   * @param int $year
   * @param int $month
   * @param int $day
   * @return Event[]|ICollection
   */
  function getForDate($year, $month, $day) {
    if($this->events === NULL) {
      $this->loadEvents($year, $month);
    }
    $events = [];
    foreach($this->events as $event) {
      $startTS = mktime(0, 0, 0, (int) $month, (int) $day, (int) $year);
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
  
  /**
   * @param int $year
   * @param int $month
   * @param int $day
   * @return bool
   */
  function isForDate($year, $month, $day) {
    return (bool) count($this->getForDate($year, $month, $day));
  }
  
  /**
   * Get ongoing events
   * 
   * @return EventDummy[]
   */
  function getCurrentEvents(): array {
    $return = $this->cache->load("events");
    if($return === NULL) {
      $events = $this->orm->events->findForTime();
      foreach($events as $event) {
        $return[] = $event->dummy();
      }
      if($return === NULL) {
        $return = [];
      }
      $this->cache->save("events", $return, [Cache::EXPIRE => "15 minutes"]);
    }
    return $return;
  }
  
  /**
   * Calculate current bonus for adventures
   * 
   * @param int $baseIncome
   * @return int
   */
  function calculateAdventuresBonus(int $baseIncome): int {
    $bonus = 0;
    $events = $this->getCurrentEvents();
    foreach($events as $event) {
      if($event->adventuresBonus) {
        $bonus += $event->adventuresBonus;
      }
    }
    return (int) ($baseIncome / 100 * $bonus);
  }
  
  /**
   * Calculate current bonus for work
   * 
   * @param int $baseIncome
   * @return int
   */
  function calculateWorkBonus(int $baseIncome): int {
    $bonus = 0;
    $events = $this->getCurrentEvents();
    foreach($events as $event) {
      if($event->workBonus) {
        $bonus += $event->workBonus;
      }
    }
    return (int) ($baseIncome / 100 * $bonus);
  }
  
  /**
   * Calculate current bonus for praying
   * 
   * @param int $baseValue
   * @return int
   */
  function calculatePrayerLifeBonus(int $baseValue): int {
    $bonus = 0;
    $events = $this->getCurrentEvents();
    foreach($events as $event) {
      if($event->prayerLifeBonus) {
        $bonus += $event->prayerLifeBonus;
      }
    }
    return (int) ($baseValue / 100 * $bonus);
  }
  
  /**
   * Calculate current discount for training
   * 
   * @param int $basePrice
   * @return int
   */
  function calculateTrainingDiscount(int $basePrice): int {
    $discount = 0;
    $events = $this->getCurrentEvents();
    foreach($events as $event) {
      if($event->trainingDiscount) {
        $discount += $event->trainingDiscount;
      }
    }
    return (int) ($basePrice / 100 * $discount);
  }
  
  /**
   * Calculate current discount for shopping
   * 
   * @param int $basePrice
   * @return int
   */
  function calculateShoppingDiscount(int $basePrice): int {
    $discount = 0;
    $events = $this->getCurrentEvents();
    foreach($events as $event) {
      if($event->shoppingDiscount) {
        $discount += $event->shoppingDiscount;
      }
    }
    return (int) ($basePrice / 100 * $discount);
  }
  
  /**
   * Get current shopping discount
   *
   * @return int
   */
  function getShoppingDiscount(): int {
    $discount = 0;
    $events = $this->getCurrentEvents();
    foreach($events as $event) {
      if($event->shoppingDiscount) {
        $discount += $event->shoppingDiscount;
      }
    }
    return $discount;
  }
  
  /**
   * Calculate current discount for repairing castles and monasteries
   * 
   * @param int $basePrice
   * @return int
   */
  function calculateRepairingDiscount(int $basePrice): int {
    $discount = 0;
    $events = $this->getCurrentEvents();
    foreach($events as $event) {
      if($event->repairingDiscount) {
        $discount += $event->repairingDiscount;
      }
    }
    return (int) ($basePrice / 100) * $discount;
  }
}
?>