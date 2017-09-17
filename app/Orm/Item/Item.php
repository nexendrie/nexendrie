<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * Item
 *
 * @author Jakub Konečný
 * @property int $id {primary}
 * @property string $name
 * @property string $description
 * @property int $price
 * @property-read string $priceT {virtual}
 * @property Shop|NULL $shop {m:1 Shop::$items}
 * @property string $type {enum self::TYPE_*} {default self::TYPE_ITEM}
 * @property int $strength {default 0}
 * @property OneHasMany|UserItem[] $userItems {1:m UserItem::$item}
 * @property-read string $typeCZ {virtual}
 * @property OneHasMany|ItemSet[] $weaponSets {1:m ItemSet::$weapon}
 * @property OneHasMany|ItemSet[] $armorSets {1:m ItemSet::$armor}
 * @property OneHasMany|ItemSet[] $helmetSets {1:m ItemSet::$helmet}
 */
class Item extends \Nextras\Orm\Entity\Entity {
  public const TYPE_ITEM = "item";
  public const TYPE_WEAPON = "weapon";
  public const TYPE_ARMOR = "armor";
  public const TYPE_HELMET = "helmet";
  public const TYPE_POTION = "potion";
  public const TYPE_MATERIAL = "material";
  public const TYPE_CHARTER = "charter";
  public const TYPE_INTIMACY_BOOST = "intimacy_boost";
  
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel) {
    $this->localeModel = $localeModel;
  }
  
  /**
   * @return string[]
   */
  static function getTypes(): array {
    return [
      self::TYPE_ITEM => "Věc",
      self::TYPE_WEAPON => "Zbraň",
      self::TYPE_ARMOR => "Brnění",
      self::TYPE_HELMET => "Helma",
      self::TYPE_POTION => "Lektvar",
      self::TYPE_POTION => "Surovina",
      self::TYPE_CHARTER => "Listina",
      self::TYPE_INTIMACY_BOOST => "Zvýšení důvěrnosti",
    ];
  }
  
  /**
   * @return string[]
   */
  static function getCommonTypes(): array {
    return [
      self::TYPE_ITEM, self::TYPE_MATERIAL, self::TYPE_CHARTER, self::TYPE_INTIMACY_BOOST
    ];
  }
  
  /**
   * @return string[]
   */
  static function getEquipmentTypes(): array {
    return [
      self::TYPE_WEAPON, self::TYPE_ARMOR, self::TYPE_HELMET
    ];
  }
  
  /**
   * @return string[]
   */
  static function getNotForSale(): array {
    return [
      self::TYPE_CHARTER, self::TYPE_INTIMACY_BOOST
    ];
  }
  
  protected function getterPriceT(): string {
    return $this->localeModel->money($this->price);
  }
  
  protected function getterTypeCZ(): string {
    return self::getTypes()[$this->type];
  }
}
?>