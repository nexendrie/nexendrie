<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Event,
    Nette\Utils\DateTime;

/**
 * Events Model
 *
 * @author Jakub Konečný
 */
class Events extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Caching\Cache */
  protected $cache;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var \Nexendrie\Model\SettingsRepository */
  protected $sr;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Caching\Cache $cache, \Nette\Security\User $user, \Nexendrie\Model\SettingsRepository $sr) {
    $this->orm = $orm;
    $this->cache = $cache;
    $this->user = $user;
    $this->sr = $sr;
  }
  
  /**
   * Get list of all events
   * 
   * @return Event[]
   */
  function listOfEvents() {
    return $this->orm->events->findAll();
  }
  
  /**
   * Get details of specified event
   * 
   * @param int $id
   * @return Event
   * @throws EventNotFoundException
   */
  function getEvent($id) {
    $event = $this->orm->events->getById($id);
    if(!$event) throw new EventNotFoundException;
    else return $event;
  }
  
  /**
   * Add new event
   * 
   * @param array $data
   * @return void
   */
  function addEvent(array $data) {
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
  function editEvent($id, array $data) {
    $event = $this->orm->events->getById($id);
    if(!$event) throw new EventNotFoundException;
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
  function deleteEvent($id) {
    $event = $this->orm->events->getById($id);
    if(!$event) throw new EventNotFoundException;
    elseif($event->start < time()) throw new CannotDeleteStartedEventException;
    else $this->orm->events->removeAndFlush($event);
  }
}

class EventNotFoundException extends RecordNotFoundException {
  
}

class CannotDeleteStartedEventException extends AccessDeniedException {
  
}
?>