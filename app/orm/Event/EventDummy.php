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
  
  function __construct(Event $event) {
    $this->id = $event->id;
    $this->name = $event->name;
    $this->description = $event->description;
    $this->start = $event->startAt;
    $this->end = $event->endAt;
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