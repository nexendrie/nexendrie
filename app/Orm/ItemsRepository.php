<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Item|null getById(int $id)
 * @method Item|null getBy(array $conds)
 * @method ICollection|Item[] findBy(array $conds)
 * @method ICollection|Item[] findAll()
 */
final class ItemsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Item::class];
  }
  
  /**
   * @return Item[]|ICollection
   */
  public function findWeapons(): ICollection {
    return $this->findBy(["type" => Item::TYPE_WEAPON]);
  }
  
  /**
   * @return Item[]|ICollection
   */
  public function findArmors(): ICollection {
    return $this->findBy(["type" => Item::TYPE_ARMOR]);
  }
  
  /**
   * @return Item[]|ICollection
   */
  public function findHelmets(): ICollection {
    return $this->findBy(["type" => Item::TYPE_HELMET]);
  }
  
  /**
   * @param Shop|int $shop
   * @return Item[]|ICollection
   */
  public function findByShop($shop): ICollection {
    return $this->findBy(["shop" => $shop]);
  }
}
?>