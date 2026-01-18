<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Skill
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property int $price
 * @property int $maxLevel
 * @property string $type {enum static::TYPE_*}
 * @property string|null $stat {enum static::STAT_*} {default null}
 * @property-read string|null $statCZ {virtual}
 * @property int $statIncrease {default 0}
 * @property int $created
 * @property int $updated
 * @property OneHasMany|Job[] $jobs {1:m Job::$neededSkill}
 * @property OneHasMany|UserSkill[] $userSkills {1:m UserSkill::$skill}
 * @property OneHasMany|Guild[] $guilds {1:m Guild::$skill}
 * @property-read string $effect {virtual}
 */
final class Skill extends BaseEntity
{
    public const string TYPE_WORK = "work";
    public const string TYPE_COMBAT = "combat";
    public const string STAT_HITPOINTS = "hitpoints";
    public const string STAT_DAMAGE = "damage";
    public const string STAT_ARMOR = "armor";
    public const string STAT_INITIATIVE = "initiative";

    /**
     * @return array<string, string>
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_WORK => "práce",
            self::TYPE_COMBAT => "boj",
        ];
    }

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

    protected function getterStatCZ(): ?string
    {
        return ($this->stat !== null) ? self::getStats()[$this->stat] : null;
    }

    protected function getterEffect(): string
    {
        if ($this->type === self::TYPE_WORK) {
            return "";
        }
        return $this->statCZ . " +" . $this->statIncrease;
    }
}
