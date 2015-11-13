<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Monastery
 *
 * @author Jakub Konečný
 * @property string $name
 * @property User $leader {m:1 User::$monasteriesLed}
 * @property Town $town {m:1 Town::$monasteries}
 * @property int $founded
 * @property int $money
 * @property int $level {default 1}
 * @property int $hp {default 100}
 * @property OneHasMany|User[] $members {1:m User::$monastery}
 * @property OneHasMany|MonasteryDonation[] $donations {1:m MonasteryDonation::$monastery}
 * @property-read string $foundedAt {virtual}
 * @property-read string $moneyT {virtual}
 * @property-read int $prayerLife {virtual}
 * @property-read int $upgradePrice {virtual}
 */
class Monastery extends \Nextras\Orm\Entity\Entity {
  const MAX_LEVEL = 6;
  const BASE_UPGRADE_PRICE = 700;
  
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterFoundedAt() {
    return $this->localeModel->formatDateTime($this->founded);
  }
  
  protected function setterLevel($value) {
    if($value < 1) return 1;
    elseif($value > self::MAX_LEVEL) return self::MAX_LEVEL;
    else return $value;
  }
  
  protected function setterHp($value) {
   if($value < 1) return 1;
   elseif($value > 100) return 100;
   else return $value;
  }
  
  protected function getterMoneyT() {
    return $this->localeModel->money($this->money);
  }
  
  protected function getterPrayerLife() {
    if($this->hp <= 30) return 0;
    else return 2 + ($this->level * 2);
  }
  
  protected function getterUpgradePrice() {
    if($this->level === 1) return self::BASE_UPGRADE_PRICE;
    elseif($this->level === self::MAX_LEVEL) return 0;
    $price = self::BASE_UPGRADE_PRICE;
    for($i = 2; $i < $this->level + 1; $i++) {
      $price += (int) (self::BASE_UPGRADE_PRICE / self::MAX_LEVEL);
    }
    return $price;
  }
  
  /**
   * @return MonasteryDummy
   */
  function dummy() {
    return new MonasteryDummy($this);
  }
  
  /**
   * @return array
   */
  function dummyArray() {
    return $this->dummy()->toArray();
  }
}
?>