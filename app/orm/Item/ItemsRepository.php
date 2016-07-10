<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class ItemsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Item::class];
  }
  
  /**
   * @param int $id
   * @return Item|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * @return Item[]
   */
  function findWeapons() {
    return $this->findBy(array("type" => Item::TYPE_WEAPON));
  }
  
  /**
   * @return Item[]
   */
  function findArmors() {
    return $this->findBy(array("type" => Item::TYPE_ARMOR));
  }
  
  /**
   * @return Item[]
   */
  function findHelmets() {
    return $this->findBy(array("type" => Item::TYPE_HELMET));
  }
}
?>