<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Town
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property User $owner {m:1 User::$ownedTowns}
 * @property int $price {default 5000}
 * @property bool $onMarket {default 0}
 * @property OneHasMany|User[] $denizens {1:m User::$town}
 * @property OneHasMany|Monastery[] $monasteries {1:m Monastery::$town}
 * @property-read string $priceT {virtual}
 */
class Town extends \Nextras\Orm\Entity\Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  protected function getterPriceT() {
    return $this->localeModel->money($this->price);
  }
  
  /**
   * @return \Nexendrie\Orm\TownDummy
   */
  function dummy() {
    return new TownDummy($this);
  }
  
  /**
   * @return array
   */
  function dummyArray() {
    return $this->dummy()->toArray();
  }
}
?>