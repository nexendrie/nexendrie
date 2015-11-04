<?php
namespace Nexendrie\Orm;

/**
 * ItemDummy
 *
 * @author Jakub Konečný
 * @property-read int $id
 * @property string $name
 * @property string $description
 * @property int $price
 * @property int|NULL $shop
 * @property string $type
 * @property int $strength
 */
class ItemDummy extends \Nette\Object {
  /** @var int */
  protected $id;
  /** @var string */
  protected $name;
  /** @var string */
  protected $description;
  /** @var int */
  protected $price;
  /** @var int */
  protected $shop;
  /** @var string */
  protected $type;
  /** @var int */
  protected $strength;
  
  function __construct(Item $item) {
    $this->id = $item->id;
    $this->name = $item->name;
    $this->description = $item->description;
    $this->price = $item->price;
    $this->shop = (empty($item->shop)) ? NULL : $item->shop->id;
    $this->type = $item->type;
    $this->strength = $item->strength;
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