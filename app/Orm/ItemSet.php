<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nexendrie\Utils\Numbers;
use HeroesofAbenez\Combat\CharacterEffect;
use HeroesofAbenez\Combat\SkillSpecial;
use HeroesofAbenez\Combat\Character;
use HeroesofAbenez\Combat\ICharacterEffectsProvider;

/**
 * ItemSet
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property Item|null $weapon {m:1 Item::$weaponSets}
 * @property Item|null $armor {m:1 Item::$armorSets}
 * @property Item|null $helmet {m:1 Item::$helmetSets}
 * @property string $stat {enum static::STAT_*}
 * @property int $bonus
 * @property int $created
 * @property int $updated
 * @property-read string $effect {virtual}
 */
final class ItemSet extends BaseEntity implements ICharacterEffectsProvider {
  public const STAT_DAMAGE = "damage";
  public const STAT_ARMOR = "armor";
  public const STAT_HITPOINTS = "hitpoints";
  public const STAT_INITIATIVE = "initiative";
  
  /**
   * @return array<string, string>
   */
  public static function getStats(): array {
    return [
      static::STAT_HITPOINTS => "maximum životů",
      static::STAT_DAMAGE => "poškození",
      static::STAT_ARMOR => "brnění",
      static::STAT_INITIATIVE => "iniciativa",
    ];
  }
  
  public function setterBonus(int $value): int {
    return Numbers::range($value, 0, 99);
  }
  
  protected function getterEffect(): string {
    return static::getStats()[$this->stat] . " +" . $this->bonus;
  }
  
  public function getCombatEffects(): array {
    $bonusStats = [
      static::STAT_HITPOINTS => Character::STAT_MAX_HITPOINTS, static::STAT_DAMAGE => Character::STAT_DAMAGE,
      static::STAT_ARMOR => Character::STAT_DEFENSE, static::STAT_INITIATIVE => Character::STAT_INITIATIVE,
    ];
    $stats = [
      "id" => "itemSet{$this->id}BonusEffect", "type" => SkillSpecial::TYPE_BUFF, "value" => $this->bonus,
      "duration" => CharacterEffect::DURATION_COMBAT, "valueAbsolute" => true,
      "stat" => $bonusStats[$this->stat],
    ];
    return [new CharacterEffect($stats)];
  }
}
?>