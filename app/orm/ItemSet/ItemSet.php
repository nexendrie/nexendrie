<?php
namespace Nexendrie\Orm;

/**
 * ItemSet
 *
 * @author Jakub Konečný
 * @property string $name
 * @property Item|NULL $weapon
 * @property Item|NULL $armor
 * @property Item|NULL $helmet
 * @property string $stat {enum self::STAT_*}
 * @property int $bonus
 * @property-read string $effect {virtual}
 */
class ItemSet extends \Nextras\Orm\Entity\Entity {
  const STAT_DAMAGE = "damage";
  const STAT_ARMOR = "armor";
  const STAT_HITPOINTS = "hitpoints";
  
  /**
   * @return string[]
   */
  static function getStats() {
    return array(
      self::STAT_HITPOINTS => "maximum životů",
      self::STAT_DAMAGE => "poškození",
      self::STAT_ARMOR => "brnění",
    );
  }
  
  function setterBonus($value) {
    if($value < 0) return 0;
    elseif($value > 99) return 99;
    else return $value;
  }
  
  protected function getterEffect() {
    return self::getStats()[$this->stat] . " +" . $this->bonus;
  }
  
  /**
   * @return \Nexendrie\Orm\ItemSetDummy
   */
  function dummy() {
    return new ItemSetDummy($this);
  }
  
  /**
   * @return array
   */
  function dummyArray() {
    return $this->dummy()->toArray();
  }
}
?>