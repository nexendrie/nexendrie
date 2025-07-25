<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;
use Nexendrie\Utils\Numbers;

/**
 * Order
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $description
 * @property int $level {default 1}
 * @property int $created
 * @property-read string $createdAt {virtual}
 * @property int $updated
 * @property int $money {default 0}
 * @property OneHasMany|User[] $members {1:m User::$order, orderBy=[orderRank,DESC]}
 * @property OneHasMany|OrderFee[] $fees {1:m OrderFee::$order}
 * @property OneHasMany|ChatMessage[] $chatMessages {1:m ChatMessage::$order}
 * @property-read int $upgradePrice {virtual}
 * @property-read float $adventuresBonusIncome {virtual}
 */
final class Order extends BaseEntity {
  public const MAX_LEVEL = 6;
  public const BASE_UPGRADE_PRICE = 800;
  public const ADVENTURE_INCOME_BONUS_PER_LEVEL = 2.5;

  private \Nexendrie\Model\Locale $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  protected function setterLevel(int $value): int {
    return Numbers::range($value, 1, self::MAX_LEVEL);
  }
  
  protected function getterCreatedAt(): string {
    return $this->localeModel->formatDateTime($this->created);
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

  protected function getterAdventuresBonusIncome(): float {
    return ($this->level - 1) * self::ADVENTURE_INCOME_BONUS_PER_LEVEL;
  }
}
?>