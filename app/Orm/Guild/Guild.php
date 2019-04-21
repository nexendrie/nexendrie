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
 * @property int $founded
 * @property-read string $foundedAt {virtual}
 * @property Town $town {m:1 Town::$guilds}
 * @property int $money {default 0}
 * @property Skill $skill {m:1 Skill::$guilds}
 * @property-read string $moneyT {virtual}
 * @property OneHasMany|User[] $members {1:m User::$guild, orderBy=[guildRank,DESC]}
 * @property OneHasMany|GuildFee[] $fees {1:m GuildFee::$guild}
 * @property OneHasMany|ChatMessage[] $chatMessages {1:m ChatMessage::$guild}
 * @property-read int $upgradePrice {virtual}
 * @property-read string $upgradePriceT {virtual}
 * @property-read int $jobBonusIncome {virtual}
 */
final class Guild extends \Nextras\Orm\Entity\Entity {
  public const MAX_LEVEL = 6;
  public const BASE_UPGRADE_PRICE = 700;
  public const JOB_INCOME_BONUS_PER_LEVEL = 1;
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  protected function setterLevel(int $value): int {
    return Numbers::range($value, 1, static::MAX_LEVEL);
  }
  
  protected function getterFoundedAt(): string {
    return $this->localeModel->formatDateTime($this->founded);
  }
  
  protected function getterMoneyT(): string {
    return $this->localeModel->money($this->money);
  }
  
  protected function getterUpgradePrice(): int {
    if($this->level === static::MAX_LEVEL) {
      return 0;
    }
    $price = static::BASE_UPGRADE_PRICE;
    for($i = 2; $i < $this->level + 1; $i++) {
      $price += (int) (static::BASE_UPGRADE_PRICE / static::MAX_LEVEL);
    }
    return $price;
  }
  
  protected function getterUpgradePriceT(): string {
    return $this->localeModel->money($this->upgradePrice);
  }

  protected function getterJobBonusIncome() {
    return ($this->level - 1) * static::JOB_INCOME_BONUS_PER_LEVEL;
  }
  
  public function onBeforeInsert(): void {
    parent::onBeforeInsert();
    $this->founded = time();
  }
}
?>