<?php
namespace Nexendrie\Orm;

/**
 * MonasteryDummy
 *
 * @author Jakub Konečný
 * @property int $id
 * @property string $name
 * @property int $leader
 * @property int $town
 * @property int $founded
 * @property int $money
 */
class MonasteryDummy extends DummyEntity {
  /** @var int */
  protected $id;
  /** @var string */
  protected $name;
  /** @var int */
  protected $leader;
  /** @var int */
  protected $town;
  /** @var int */
  protected $founded;
  /** @var int */
  protected $money;
  
  function __construct(Monastery $monastery) {
    $this->id = $monastery->id;
    $this->name = $monastery->name;
    $this->leader = $monastery->leader->id;
    $this->town = $monastery->town->id;
    $this->founded = $monastery->founded;
    $this->money = $monastery->money;
  }
}
?>