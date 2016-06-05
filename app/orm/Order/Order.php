<?php
namespace Nexendrie\Orm;

/**
 * Order
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property int $level {default 1}
 * @property int $founded
 * @property-read string $foundedAt {virtual}
 * @property int $money
 * @property-read string $moneyT {virtual}
 * @property OneHasMany|User[] $members {1:m User::$order order:orderRank,DESC}
 * @property-read int $upgradePrice {virtual}
 * @property-read string $upgradePriceT {virtual}
 */
class Order extends \Nextras\Orm\Entity\Entity {
  const MAX_LEVEL = 6;
  const BASE_UPGRADE_PRICE = 700;
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function setterLevel($value) {
    if($value < 1) return 1;
    elseif($value > self::MAX_LEVEL) return self::MAX_LEVEL;
    else return $value;
  }
  
  protected function getterFoundedAt() {
    return $this->localeModel->formatDateTime($this->founded);
  }
  
  protected function getterMoneyT() {
    return $this->localeModel->money($this->money);
  }
  
  protected function getterUpgradePrice() {
    if($this->level === self::MAX_LEVEL) return 0;
    $price = self::BASE_UPGRADE_PRICE;
    for($i = 2; $i < $this->level + 1; $i++) {
      $price += (int) (self::BASE_UPGRADE_PRICE / self::MAX_LEVEL);
    }
    return $price;
  }
  
  protected function getterUpgradePriceT() {
    return $this->localeModel->money($this->upgradePrice);
  }
}
?>