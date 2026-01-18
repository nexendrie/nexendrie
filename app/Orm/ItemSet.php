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
final class ItemSet extends BaseEntity implements ICharacterEffectsProvider
{
    public const string STAT_DAMAGE = "damage";
    public const string STAT_ARMOR = "armor";
    public const string STAT_HITPOINTS = "hitpoints";
    public const string STAT_INITIATIVE = "initiative";

    /**
     * @return array<string, string>
     */
    public static function getStats(): array
    {
        return [
            self::STAT_HITPOINTS => "maximum životů",
            self::STAT_DAMAGE => "poškození",
            self::STAT_ARMOR => "brnění",
            self::STAT_INITIATIVE => "iniciativa",
        ];
    }

    public function setterBonus(int $value): int
    {
        return Numbers::range($value, 0, 99);
    }

    protected function getterEffect(): string
    {
        return self::getStats()[$this->stat] . " +" . $this->bonus;
    }

    public function getCombatEffects(): array
    {
        $bonusStats = [
            self::STAT_HITPOINTS => Character::STAT_MAX_HITPOINTS, self::STAT_DAMAGE => Character::STAT_DAMAGE,
            self::STAT_ARMOR => Character::STAT_DEFENSE, self::STAT_INITIATIVE => Character::STAT_INITIATIVE,
        ];
        $stats = [
            "id" => "itemSet{$this->id}BonusEffect", "type" => SkillSpecial::TYPE_BUFF, "value" => $this->bonus,
            "duration" => CharacterEffect::DURATION_COMBAT, "valueAbsolute" => true,
            "stat" => $bonusStats[$this->stat],
        ];
        return [new CharacterEffect($stats)];
    }
}
