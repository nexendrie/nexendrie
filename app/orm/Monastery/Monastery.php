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
 * @property OneHasMany|User[] $members {1:m User::$monastery}
 * @property OneHasMany|MonasteryDonation[] $donations {1:m MonasteryDonation::$monastery}
 * @property-read string $foundedAt {virtual}
 * @property-read string $moneyT {virtual}
 */
class Monastery extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterFoundedAt() {
    return $this->localeModel->formatDateTime($this->founded);
  }
  
  protected function getterMoneyT() {
    return $this->localeModel->money($this->money);
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