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
 * @property Shop|null $shop {m:1 Shop::$items}
 * @property string $type {enum static::TYPE_*} {default static::TYPE_ITEM}
 * @property int $strength {default 0}
 * @property int $created
 * @property int $updated
 * @property OneHasMany|UserItem[] $userItems {1:m UserItem::$item}
 * @property-read string $typeCZ {virtual}
 * @property OneHasMany|ItemSet[] $weaponSets {1:m ItemSet::$weapon}
 * @property OneHasMany|ItemSet[] $armorSets {1:m ItemSet::$armor}
 * @property OneHasMany|ItemSet[] $helmetSets {1:m ItemSet::$helmet}
 */
final class Item extends BaseEntity {
  public const TYPE_ITEM = "item";
  public const TYPE_WEAPON = "weapon";
  public const TYPE_ARMOR = "armor";
  public const TYPE_HELMET = "helmet";
  public const TYPE_AMULET = "amulet";
  public const TYPE_POTION = "potion";
  public const TYPE_MATERIAL = "material";
  public const TYPE_CHARTER = "charter";
  public const TYPE_INTIMACY_BOOST = "intimacy_boost";

  protected \Nexendrie\Model\Locale $localeModel;
  
  public function injectLocaleModel(\Nexendrie\Model\Locale $localeModel): void {
    $this->localeModel = $localeModel;
  }
  
  /**
   * @return string[]
   */
  public static function getTypes(): array {
    return [
      static::TYPE_ITEM => "Věc",
      static::TYPE_WEAPON => "Zbraň",
      static::TYPE_ARMOR => "Brnění",
      static::TYPE_HELMET => "Helma",
      static::TYPE_AMULET => "Amulet",
      static::TYPE_POTION => "Lektvar",
      static::TYPE_MATERIAL => "Surovina",
      static::TYPE_CHARTER => "Listina",
      static::TYPE_INTIMACY_BOOST => "Zvýšení důvěrnosti",
    ];
  }
  
  /**
   * @return string[]
   */
  public static function getCommonTypes(): array {
    return [
      static::TYPE_ITEM, static::TYPE_MATERIAL, static::TYPE_CHARTER, static::TYPE_INTIMACY_BOOST
    ];
  }
  
  /**
   * @return string[]
   */
  public static function getEquipmentTypes(): array {
    return [
      static::TYPE_WEAPON, static::TYPE_ARMOR, static::TYPE_HELMET, static::TYPE_AMULET,
    ];
  }
  
  /**
   * @return string[]
   */
  public static function getNotForSale(): array {
    return [
      static::TYPE_CHARTER, static::TYPE_INTIMACY_BOOST
    ];
  }
  
  protected function getterPriceT(): string {
    return $this->localeModel->money($this->price);
  }
  
  protected function getterTypeCZ(): string {
    return static::getTypes()[$this->type];
  }
}
?>