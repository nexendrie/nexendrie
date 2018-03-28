<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * ItemSet
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property Item|NULL $weapon {m:1 Item::$weaponSets}
 * @property Item|NULL $armor {m:1 Item::$armorSets}
 * @property Item|NULL $helmet {m:1 Item::$helmetSets}
 * @property string $stat {enum static::STAT_*}
 * @property int $bonus
 * @property-read string $effect {virtual}
 */
class ItemSet extends \Nextras\Orm\Entity\Entity {
  public const STAT_DAMAGE = "damage";
  public const STAT_ARMOR = "armor";
  public const STAT_HITPOINTS = "hitpoints";
  
  /**
   * @return string[]
   */
  public static function getStats(): array {
    return [
      static::STAT_HITPOINTS => "maximum životů",
      static::STAT_DAMAGE => "poškození",
      static::STAT_ARMOR => "brnění",
    ];
  }
  
  public function setterBonus(int $value): int {
    if($value < 0) {
      return 0;
    } elseif($value > 99) {
      return 99;
    }
    return $value;
  }
  
  protected function getterEffect(): string {
    return static::getStats()[$this->stat] . " +" . $this->bonus;
  }
}
?>