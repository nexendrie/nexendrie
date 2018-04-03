<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nexendrie\Utils\Numbers,
    HeroesofAbenez\Combat\CharacterEffect,
    HeroesofAbenez\Combat\SkillSpecial,
    HeroesofAbenez\Combat\ICharacterEffectProvider;

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
class ItemSet extends \Nextras\Orm\Entity\Entity implements ICharacterEffectProvider {
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
    return Numbers::range($value, 0, 99);
  }
  
  protected function getterEffect(): string {
    return static::getStats()[$this->stat] . " +" . $this->bonus;
  }
  
  public function toCombatEffect(): CharacterEffect {
    $bonusStats = [
      static::STAT_HITPOINTS => SkillSpecial::STAT_HITPOINTS, static::STAT_DAMAGE => SkillSpecial::STAT_DAMAGE,
      static::STAT_ARMOR => SkillSpecial::STAT_DEFENSE,
    ];
    $stats = [
      "id" => "itemSet{$this->id}BonusEffect", "type" => SkillSpecial::TYPE_BUFF, "value" => $this->bonus,
      "duration" => CharacterEffect::DURATION_COMBAT, "source" => CharacterEffect::SOURCE_EQUIPMENT,
      "stat" => $bonusStats[$this->stat],
    ];
    return new CharacterEffect($stats);
  }
}
?>