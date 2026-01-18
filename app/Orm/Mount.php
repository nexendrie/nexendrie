<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;
use Nexendrie\Utils\Numbers;
use HeroesofAbenez\Combat\CharacterEffect;
use HeroesofAbenez\Combat\SkillSpecial;
use HeroesofAbenez\Combat\Character;
use HeroesofAbenez\Combat\ICharacterEffectsProvider;

/**
 * Mount
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $gender {enum static::GENDER_*} {default static::GENDER_YOUNG}
 * @property MountType $type {m:1 MountType::$mounts}
 * @property User $owner {m:1 User::$mounts}
 * @property int $price
 * @property bool $onMarket {default false}
 * @property int $created
 * @property int $updated
 * @property int $hp {default 100}
 * @property int $damage {default 0}
 * @property int $armor {default 0}
 * @property bool $autoFeed {default false}
 * @property OneHasMany|UserAdventure[] $adventures {1:m UserAdventure::$mount}
 * @property-read string $genderCZ {virtual}
 * @property-read string $createdAt {virtual}
 * @property-read int $baseDamage {virtual}
 * @property-read int $baseArmor {virtual}
 * @property-read int $maxDamage {virtual}
 * @property-read int $maxArmor {virtual}
 * @property-read int $damageTrainingCost {virtual}
 * @property-read int $armorTrainingCost {virtual}
 * @property-read string $typeGenderName {virtual}
 */
final class Mount extends BaseEntity implements ICharacterEffectsProvider
{
    private \Nexendrie\Model\Locale $localeModel;
    private \Nexendrie\Model\Events $eventsModel;

    public const string GENDER_MALE = "male";
    public const string GENDER_FEMALE = "female";
    public const string GENDER_YOUNG = "young";
    public const int HP_DECREASE_ADVENTURE = 5;
    public const int HP_DECREASE_TRAINING = 10;
    public const int HP_DECREASE_WEEKLY = 5;

    public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void
    {
        $this->localeModel = $localeModel;
    }

    public function injectEventsModel(\Nexendrie\Model\Events $eventsModel): void
    {
        $this->eventsModel = $eventsModel;
    }

    /**
     * @return array<string, string>
     */
    public static function getGenders(): array
    {
        return [
            self::GENDER_MALE => "hřebec",
            self::GENDER_FEMALE => "klisna",
            self::GENDER_YOUNG => "mládě"
        ];
    }

    protected function setterHp(int $value): int
    {
        return Numbers::range($value, 0, 100);
    }

    protected function setterDamage(int $value): int
    {
        return Numbers::range($value, $this->baseDamage, $this->maxDamage);
    }

    protected function setterArmor(int $value): int
    {
        return Numbers::range($value, $this->baseArmor, $this->maxArmor);
    }

    protected function getterGenderCZ(): string
    {
        return self::getGenders()[$this->gender];
    }

    protected function getterCreatedAt(): string
    {
        return $this->localeModel->formatDateTime($this->created);
    }

    protected function getterBaseDamage(): int
    {
        return $this->type->damage;
    }

    protected function getterBaseArmor(): int
    {
        return $this->type->armor;
    }

    protected function getterMaxDamage(): int
    {
        return max($this->type->damage * 2, 1);
    }

    protected function getterMaxArmor(): int
    {
        return max($this->type->armor * 2, 1);
    }

    protected function getterDamageTrainingCost(): int
    {
        if ($this->damage >= $this->maxDamage) {
            return 0;
        }
        $basePrice = ($this->damage - $this->baseDamage + 1) * 30;
        $basePrice -= $this->eventsModel->calculateTrainingDiscount($basePrice);
        return $basePrice;
    }

    protected function getterArmorTrainingCost(): int
    {
        if ($this->armor >= $this->maxArmor) {
            return 0;
        }
        $basePrice = ($this->armor - $this->baseArmor + 1) * 30;
        $basePrice -= $this->eventsModel->calculateTrainingDiscount($basePrice);
        return $basePrice;
    }

    protected function getterTypeGenderName(): string
    {
        return match ($this->gender) {
            self::GENDER_FEMALE => $this->type->femaleName,
            self::GENDER_YOUNG => $this->type->youngName,
            default => $this->type->maleName,
        };
    }

    public function onBeforeInsert(): void
    {
        parent::onBeforeInsert();
        if ($this->price === 0) {
            $this->price = $this->type->price;
        }
        if ($this->damage === 0) {
            $this->damage = $this->type->damage;
        }
        if ($this->armor === 0) {
            $this->armor = $this->type->armor;
        }
        if ($this->owner->id === 0) {
            $this->onMarket = true;
        }
    }

    public function toCombatDamageEffect(): CharacterEffect
    {
        $stats = [
            "id" => "mount{$this->id}DamageBonusEffect", "type" => SkillSpecial::TYPE_BUFF, "value" => $this->damage,
            "duration" => CharacterEffect::DURATION_COMBAT, "valueAbsolute" => true,
            "stat" => Character::STAT_DAMAGE,
        ];
        return new CharacterEffect($stats);
    }

    public function toCombatDefenseEffect(): CharacterEffect
    {
        $stats = [
            "id" => "mount{$this->id}DefenseBonusEffect", "type" => SkillSpecial::TYPE_BUFF, "value" => $this->armor,
            "duration" => CharacterEffect::DURATION_COMBAT, "valueAbsolute" => true,
            "stat" => Character::STAT_DEFENSE,
        ];
        return new CharacterEffect($stats);
    }

    public function getCombatEffects(): array
    {
        return [$this->toCombatDamageEffect(), $this->toCombatDefenseEffect(),];
    }
}
