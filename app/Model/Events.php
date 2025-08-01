<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event as CalendarEvent;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Domain\ValueObject\Timestamp;
use Eluceo\iCal\Domain\ValueObject\UniqueIdentifier;
use Eluceo\iCal\Domain\ValueObject\Uri;
use Nexendrie\Orm\Event;
use Nette\Caching\Cache;
use Nexendrie\Orm\EventDummy;
use Nexendrie\Orm\Model as ORM;
use Nextras\Orm\Collection\ICollection;

/**
 * Events Model
 *
 * @author Jakub Konečný
 */
final class Events implements \Nexendrie\EventCalendar\EventModel {
  /** @var Event[]|ICollection */
  private ICollection $events;

  public function __construct(private readonly ORM $orm, private readonly Cache $cache, private readonly SettingsRepository $sr, private readonly \Nette\Application\LinkGenerator $lg) {
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
  public function getEvent(int $id): Event {
    $event = $this->orm->events->getById($id);
    return $event ?? throw new EventNotFoundException();
  }

  /**
   * Add new event
   */
  public function addEvent(array $data): void {
    $event = new Event();
    foreach($data as $key => $value) {
      if($key === "start" || $key === "end") {
        $value = (int) $value->getTimestamp();
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
    if($event === null) {
      throw new EventNotFoundException();
    }
    foreach($data as $key => $value) {
      if($key === "start" || $key === "end") {
        $value = (int) $value->getTimestamp();
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
    if($event === null) {
      throw new EventNotFoundException();
    } elseif($event->start < time()) {
      throw new CannotDeleteStartedEventException();
    }
    $this->orm->events->removeAndFlush($event);
  }

  /**
   * Load events from a month
   */
  public function loadEvents(int $year = null, int $month = null): void {
    $this->events = $this->orm->events->findFromMonth($year, $month);
  }

  /**
   * @return string[]
   */
  public function getForDate(int $year, int $month, int $day): array {
    if(!isset($this->events)) {
      $this->loadEvents($year, $month);
    }
    $events = [];
    foreach($this->events as $event) {
      $startTS = (int) mktime(0, 0, 0, $month, $day, $year);
      $date = new \DateTime();
      $date->setTimestamp($startTS);
      $date->modify("+1 day");
      $date->modify("-1 second");
      if($event->start <= $date->getTimestamp() && $event->end >= $startTS) {
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
    return $this->cache->load("events", function(&$dependencies): array {
      $dependencies[Cache::Expire] = "15 minutes";
      $return = [];
      $events = $this->orm->events->findForTime();
      foreach($events as $event) {
        $return[] = $event->dummy();
      }
      return $return;
    });
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

  public function getCalendar(): Calendar {
    $calendar = new Calendar();

    $events = $this->orm->events->findAll();
    foreach ($events as $event) {
      // phpcs:disable PSR2.Methods.FunctionCallSignature
      $calendarEvent = new CalendarEvent(
        new UniqueIdentifier("nexendrie" . $this->sr->settings["site"]["versionSuffix"] . "/event/" . $event->id)
      );
      $calendarEvent->setSummary($event->name);
      $calendarEvent->setDescription($event->description);
      $calendarEvent->touch(new Timestamp((new \DateTime())->setTimestamp($event->updated)));
      $calendarEvent->setOccurrence(new TimeSpan(
        new DateTime((new \DateTime())->setTimestamp($event->start), false),
        new DateTime((new \DateTime())->setTimestamp($event->end), false)
      ));
      // phpcs:enable
      $calendarEvent->setUrl(new Uri($this->lg->link("Front:Event:view", ["id" => $event->id])));
      $calendar->addEvent($calendarEvent);
    }

    return $calendar;
  }
}
?>