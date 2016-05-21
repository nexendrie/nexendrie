<?php
namespace Nexendrie\Orm;

/**
 * TownDummy
 *
 * @author Jakub Konečný
 */
class TownDummy extends DummyEntity {
  /** @var int */
  protected $id;
  /** @var string */
  protected $name;
  /** @var string */
  protected $description;
  /** @var int */
  protected $owner;
  /** @var int */
  protected $price;
  /** @var bool */
  protected $onMarket;
  
  function __construct(Town $town) {
    $this->id = $town->id;
    $this->name = $town->name;
    $this->description = $town->description;
    $this->owner = $town->owner->id;
    $this->price = $town->price;
    $this->onMarket = $town->onMarket;
  }
}
?>