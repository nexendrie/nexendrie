<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
final class ItemsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Item::class];
  }
  
  /**
   * @param int $id
   * @return Item|null
   */
  public function getById($id): ?\Nextras\Orm\Entity\IEntity {
    return $this->getBy(["id" => $id]);
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