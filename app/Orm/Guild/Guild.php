<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

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
 * @property-read int $upgradePrice {virtual}
 * @property-read string $upgradePriceT {virtual}
 */
class Guild extends \Nextras\Orm\Entity\Entity {
  public const MAX_LEVEL = 6;
  public const BASE_UPGRADE_PRICE = 700;
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function setterLevel(int $value): int {
    if($value < 1) {
      return 1;
    } elseif($value > self::MAX_LEVEL) {
      return self::MAX_LEVEL;
    }
    return $value;
  }
  
  protected function getterFoundedAt(): string {
    return $this->localeModel->formatDateTime($this->founded);
  }
  
  protected function getterMoneyT(): string {
    return $this->localeModel->money($this->money);
  }
  
  protected function getterUpgradePrice(): int {
    if($this->level === self::MAX_LEVEL) {
      return 0;
    }
    $price = self::BASE_UPGRADE_PRICE;
    for($i = 2; $i < $this->level + 1; $i++) {
      $price += (int) (self::BASE_UPGRADE_PRICE / self::MAX_LEVEL);
    }
    return $price;
  }
  
  protected function getterUpgradePriceT(): string {
    return $this->localeModel->money($this->upgradePrice);
  }
  
  protected function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->founded = time();
  }
}
?>