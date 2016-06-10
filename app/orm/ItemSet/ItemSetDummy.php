<?php
namespace Nexendrie\Orm;

/**
 * ItemSetDummy
 *
 * @author Jakub Konečný
 * @property int $id
 * @property string $name
 * @property int|NULL $weapon
 * @property int|NULL $armor
 * @property int|NULL $helmet
 * @property string $stat
 * @property int $bonus
 */
class ItemSetDummy extends DummyEntity {
  /** @var int */
  protected $id;
  /** @var string */
  protected $name;
  /** @var int */
  protected $weapon;
  /** @var int */
  protected $armor;
  /** @var int */
  protected $helmet;
  /** @var int */
  protected $stat;
  /** @var string */
  protected $bonus;
  
  function __construct(ItemSet $set) {
    $this->id = $set->id;
    $this->name = $set->name;
    $this->weapon = (empty($set->weapon)) ? NULL : $set->weapon->id;
    $this->armor = (empty($set->armor)) ? NULL : $set->armor->id;
    $this->helmet = (empty($set->helmet)) ? NULL : $set->helmet->id;
    $this->stat = $set->stat;
    $this->bonus = $set->bonus;
  }
}
?>
