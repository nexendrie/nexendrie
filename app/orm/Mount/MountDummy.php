<?php
namespace Nexendrie\Orm;

/**
 * MountDummy
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $gender
 * @property int $type
 * @property int $price
 * @property bool $onMarket
 */
class MountDummy extends \Nette\Object {
  /** @var string */
  protected $name;
  /** @var string */
  protected $gender;
  /** @var int */
  protected $type;
  /** @var int */
  protected $price;
  /** @var bool */
  protected $onMarket;
  
  function __construct(Mount $mount) {
    $this->name = $mount->name;
    $this->gender = $mount->gender;
    $this->type = $mount->type->id;
    $this->price = $mount->price;
    $this->onMarket = $mount->onMarket;
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