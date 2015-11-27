<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Item
 *
 * @author Jakub Konečný
 * @property string $name
 * @property string $description
 * @property int $price
 * @property-read string $priceT {virtual}
 * @property Shop|NULL $shop {m:1 Shop}
 * @property string $type {enum self::TYPE_*} {default self::TYPE_ITEM}
 * @property int $strength {default 0}
 * @property OneHasMany|UserItem[] $userItems {1:m UserItem::$item}
 * @property-read string $typeCZ {virtual}
 */
class Item extends \Nextras\Orm\Entity\Entity {
  const TYPE_ITEM = "item";
  const TYPE_WEAPON = "weapon";
  const TYPE_ARMOR = "armor";
  const TYPE_POTION = "potion";
  const TYPE_MATERIAL = "material";
  const TYPE_CHARTER = "charter";
  
  /** @var \Nexendrie\Model\Locale $localeModel */
  protected $localeModel;
  /** @var \Nexendrie\Model\Events */
  protected $eventsModel;
  
  function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  function injectEventsModel(\Nexendrie\Model\Events $eventsModel) {
    $this->eventsModel = $eventsModel;
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
      "charter" => "Listina",
    );
  }
  
  /**
   * @return string[]
   */
  static function getCommonTypes() {
    return array(
      "item", "material", "charter"
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
  
  protected function getterPrice($price) {
    return (int) ($price - $this->eventsModel->calculateShoppingDiscount($price));
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