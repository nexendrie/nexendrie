<?php
namespace Nexendrie\Orm;

/**
 *TownDummy
 *
 * @author Jakub Konečný
 */
class TownDummy extends \Nette\Object {
  /** @var int */
  protected $id;
  /** @var string */
  protected $name;
  /** @var string */
  protected $description;
  /** @var int */
  protected $owner;
  
  function __construct(Town $town) {
    $this->id = $town->id;
    $this->name = $town->name;
    $this->description = $town->description;
    $this->owner = $town->owner->id;
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