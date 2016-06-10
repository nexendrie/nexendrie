<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 * @method Item|NULL getById($id)
 */
class ItemsRepository extends \Nextras\Orm\Repository\Repository {
  /**
   * @return Item[]
   */
  function findWeapons() {
    return $this->findBy(array("type" => "weapon"));
  }
  
  /**
   * @return Item[]
   */
  function findArmors() {
    return $this->findBy(array("type" => "armor"));
  }
  
  /**
   * @return Item[]
   */
  function findHelmets() {
    return $this->findBy(array("type" => "helmet"));
  }
}
?>