<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Order
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $description
 * @property int $level {default 1}
 * @property int $founded
 * @property-read string $foundedAt {virtual}
 * @property int $money {default 0}
 * @property-read string $moneyT {virtual}
 * @property OneHasMany|User[] $members {1:m User::$order, orderBy=[orderRank,DESC]}
 * @property OneHasMany|OrderFee[] $fees {1:m OrderFee::$order}
 * @property OneHasMany|ChatMessage[] $chatMessages {1:m ChatMessage::$order}
 * @property-read int $upgradePrice {virtual}
 * @property-read string $upgradePriceT {virtual}
 */
class Order extends \Nextras\Orm\Entity\Entity {
  public const MAX_LEVEL = 6;
  public const BASE_UPGRADE_PRICE = 800;
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function setterLevel(int $value): int {
    if($value < 1) {
      return 1;
    } elseif($value > static::MAX_LEVEL) {
      return static::MAX_LEVEL;
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
  
  public function onBeforeInsert() {
    parent::onBeforeInsert();
    $this->founded = time();
  }
}
?>