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
 * @property string $type {enum self::TYPE_*} {default self::TYPE_ITEM}
 * @property int $strength {default 0}
 * @property OneHasMany|UserItem[] $userItems {1:m UserItem::$item}
 * @property-read string $typeCZ {virtual}
 */
class Item extends Entity {
  const TYPE_ITEM = "item";
  const TYPE_WEAPON = "weapon";
  const TYPE_ARMOR = "armor";
  const TYPE_POTION = "potion";
  const TYPE_MATERIAL = "material";
  
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  /**
   * @return string[]
   */
  static function getTypes() {
    return array(
      "item" => "Věc",
      "weapon" => "Zbraň",
      "armor" => "Brnění",
      "potion" => "Lektvar",
      "material" => "Surovina",
    );
  }
  
  /**
   * @return string[]
   */
  static function getEquipmentTypes() {
    return array(
      "weapon", "armor"
    );
  }
  
  protected function getterPriceT() {
    return $this->localeModel->money($this->price);
  }
  
  protected function getterTypeCZ() {
    return self::getTypes()[$this->type];
  }
  
  /**
   * @return \Nexendrie\Orm\ItemDummy
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