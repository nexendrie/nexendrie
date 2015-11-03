<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity;

/**
 * AdventureNpc
 *
 * @author Jakub Konečný
 * @property string $name
 * @property Adventure $adventure {m:1 Adventure::$npcs}
 * @property int $order
 * @property int $hitpoints 
 * @property int $strength
 * @property int $armor
 * @property int $reward
 * @property string $encounterText
 * @property string $victoryText
 */
class AdventureNpc extends Entity {
  /**
   * @return \Nexendrie\Orm\AdventureNpcDummy
   */
  function dummy() {
    return new AdventureNpcDummy($this);
  }
  
  /**
   * @return array
   */
  function dummyArray() {
    return $this->dummy()->toArray();
  }
}
?>