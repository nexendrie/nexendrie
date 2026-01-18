<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;
use Nexendrie\Utils\Numbers;

/**
 * Guild
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $description
 * @property int $level {default 1}
 * @property int $created
 * @property-read string $createdAt {virtual}
 * @property int $updated
 * @property Town $town {m:1 Town::$guilds}
 * @property int $money {default 0}
 * @property Skill $skill {m:1 Skill::$guilds}
 * @property OneHasMany|User[] $members {1:m User::$guild, orderBy=[guildRank,DESC]}
 * @property OneHasMany|GuildFee[] $fees {1:m GuildFee::$guild}
 * @property OneHasMany|ChatMessage[] $chatMessages {1:m ChatMessage::$guild}
 * @property-read int $upgradePrice {virtual}
 * @property-read int $jobBonusIncome {virtual}
 */
final class Guild extends BaseEntity
{
    public const int MAX_LEVEL = 6;
    public const int BASE_UPGRADE_PRICE = 700;
    public const int JOB_INCOME_BONUS_PER_LEVEL = 1;

    private \Nexendrie\Model\Locale $localeModel;

    public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void
    {
        $this->localeModel = $localeModel;
    }

    protected function setterLevel(int $value): int
    {
        return Numbers::range($value, 1, self::MAX_LEVEL);
    }

    protected function getterCreatedAt(): string
    {
        return $this->localeModel->formatDateTime($this->created);
    }

    protected function getterUpgradePrice(): int
    {
        if ($this->level === self::MAX_LEVEL) {
            return 0;
        }
        $price = self::BASE_UPGRADE_PRICE;
        for ($i = 2; $i < $this->level + 1; $i++) {
            $price += (int) (self::BASE_UPGRADE_PRICE / self::MAX_LEVEL);
        }
        return $price;
    }

    protected function getterJobBonusIncome(): int
    {
        return ($this->level - 1) * self::JOB_INCOME_BONUS_PER_LEVEL;
    }
}
