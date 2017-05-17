<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class ItemsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames(): array {
    return [Item::class];
  }
  
  /**
   * @param int $id
   * @return Item|NULL
   */
  function getById($id): ?Item {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @return Item[]|ICollection
   */
  function findWeapons(): ICollection {
    return $this->findBy(["type" => Item::TYPE_WEAPON]);
  }
  
  /**
   * @return Item[]|ICollection
   */
  function findArmors(): ICollection {
    return $this->findBy(["type" => Item::TYPE_ARMOR]);
  }
  
  /**
   * @return Item[]|ICollection
   */
  function findHelmets(): ICollection {
    return $this->findBy(["type" => Item::TYPE_HELMET]);
  }
}
?>