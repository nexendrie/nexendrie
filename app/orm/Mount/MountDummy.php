<?php
namespace Nexendrie\Orm;

/**
 * MountDummy
 *
 * @author Jakub Konečný
 * @property string $name
 * @property int $gender
 * @property int $type
 * @property int $price
 * @property bool $onMarket
 */
class MountDummy extends \Nette\Object {
  /** @var string */
  protected $name;
  /** @var int */
  protected $type;
  /** @var int */
  protected $price;
  /** @var bool */
  protected $onMarket;
  
  function __construct(Mount $mount) {
    $this->name = $mount->name;
    $this->type = $mount->type->id;
    $this->price = $mount->price;
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