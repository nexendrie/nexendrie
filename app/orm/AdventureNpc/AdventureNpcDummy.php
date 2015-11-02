<?php
namespace Nexendrie\Orm;

/**
 * AdventureNpcDummy
 *
 * @author Jakub Konečný
 * @property int $id
 * @property string $name
 * @property int $adventure
 * @property int $order
 * @property int $hitpoints 
 * @property int $strength
 * @property int $armor
 * @property int $reward
 * @property string $encounterText
 * @property string $victoryText
 */
class AdventureNpcDummy extends \Nette\Object {
  /** @var int */
  protected $id;
  /** @var string */
  protected $name;
  /** @var int */
  protected $adventure;
  /** @var int */
  protected $order;
  /** @var int */
  protected $hitpoints;
  /** @var int */
  protected $strength;
  /** @var int */
  protected $armor;
  /** @var int */
  protected $reward;
  /** @var string */
  protected $encounterText;
  /** @var string */
  protected $victoryText;
  
  function __construct(AdventureNpc $npc) {
    $this->id = $npc->id;
    $this->name = $npc->name;
    $this->adventure = $npc->adventure;
    $this->order = $npc->order;
    $this->hitpoints = $npc->hitpoints;
    $this->strength = $npc->strength;
    $this->armor = $npc->armor;
    $this->reward = $npc->reward;
    $this->encounterText = $npc->encounterText;
    $this->victoryText = $npc->victoryText;
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