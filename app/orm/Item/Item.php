<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Entity\Entity,
    Nextras\Orm\Relationships\OneHasMany;

/**
 * Item
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property int $price
 * @property-read string $priceT {virtual}
 * @property Shop $shop {m:1 Shop}
 * @property OneHasMany|UserItem[] $userItems {1:m UserItem::$item}
 */
class Item extends Entity {
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  function getterPriceT() {
    return $this->localeModel->money($this->price);
  }
  
  /**
   * @return \Nexendrie\Orm\Itemdummy
   */
  function dummy() {
    return new ItemDummy($this);
  }
  
  /**
   * @return array
   */
  function dummyArray() {
    return $this->dummy()->toArray();
  }
}
?>