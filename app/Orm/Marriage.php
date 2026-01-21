<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nexendrie\Utils\Numbers;
use HeroesofAbenez\Combat\CharacterEffect;
use HeroesofAbenez\Combat\SkillSpecial;
use HeroesofAbenez\Combat\Character;
use HeroesofAbenez\Combat\ICharacterEffectsProvider;

/**
 * Marriage
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property User $user1 {m:1 User::$sentMarriages}
 * @property User $user2 {m:1 User::$receivedMarriages}
 * @property string $status {enum static::STATUS_*} {default static::STATUS_PROPOSED}
 * @property int $divorce {default 0}
 * @property int $created
 * @property int $updated
 * @property-read string $createdAt {virtual}
 * @property int|null $accepted {default null}
 * @property-read string|null $acceptedT {virtual}
 * @property int|null $term
 * @property-read string|null $termT {virtual}
 * @property int|null $cancelled {default null}
 * @property-read string|null $cancelledT {virtual}
 * @property int $intimacy {default 0}
 * @property-read int $level {virtual}
 * @property-read int $hpIncrease {virtual}
 */
final class Marriage extends BaseEntity implements ICharacterEffectsProvider
{
    public const string STATUS_PROPOSED = "proposed";
    public const string STATUS_ACCEPTED = "accepted";
    public const string STATUS_DECLINED = "declined";
    public const string STATUS_ACTIVE = "active";
    public const string STATUS_CANCELLED = "cancelled";
    public const int MAX_INTIMACY = 1000;
    public const int INTIMACY_FOR_LEVEL = 100;
    public const int HP_INCREASE_PER_LEVEL = 2;

    private \Nexendrie\Model\Locale $localeModel;

    public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void
    {
        $this->localeModel = $localeModel;
    }

    protected function setterDivorce(int $value): int
    {
        return Numbers::clamp($value, 0, 4);
    }

    protected function getterCreatedAt(): string
    {
        return $this->localeModel->formatDateTime($this->created);
    }

    protected function getterAcceptedT(): string
    {
        if ($this->accepted === null) {
            return "";
        }
        return $this->localeModel->formatDateTime($this->accepted);
    }

    protected function getterTermT(): string
    {
        if ($this->term === null) {
            return "";
        }
        return $this->localeModel->formatDateTime($this->term);
    }

    protected function getterCancelledT(): string
    {
        if ($this->cancelled === null) {
            return "";
        }
        return $this->localeModel->formatDateTime($this->cancelled);
    }

    protected function setterIntimacy(int $value): int
    {
        return Numbers::clamp($value, 0, self::MAX_INTIMACY);
    }

    protected function getterLevel(): int
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return 0;
        }
        return (int) ($this->intimacy / self::INTIMACY_FOR_LEVEL);
    }

    protected function getterHpIncrease(): int
    {
        return $this->level * self::HP_INCREASE_PER_LEVEL;
    }

    public function onBeforeUpdate(): void
    {
        parent::onBeforeUpdate();
        if ($this->status === self::STATUS_ACCEPTED && $this->accepted === null) {
            $this->accepted = time();
        }
        if ($this->status === self::STATUS_ACCEPTED && $this->term === null) {
            $this->term = time() + (60 * 60 * 24 * 14);
        }
        if ($this->status === self::STATUS_DECLINED && $this->accepted === null) {
            $this->accepted = time();
        }
        if ($this->status === self::STATUS_CANCELLED && $this->cancelled === null) {
            $this->cancelled = time();
        }
    }

    public function getCombatEffects(): array
    {
        $stats = [
            "id" => "marriageBonusEffect", "type" => SkillSpecial::TYPE_BUFF, "value" => $this->hpIncrease,
            "duration" => CharacterEffect::DURATION_COMBAT, "valueAbsolute" => true,
            "stat" => Character::STAT_MAX_HITPOINTS,
        ];
        return [new CharacterEffect($stats)];
    }
}
