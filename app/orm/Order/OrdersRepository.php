<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class OrdersRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Order::class];
  }
  
  /**
   * @param int $id
   * @return Order|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * @param string $name
   * @return Order|NULL
   */
  function getByName($name) {
    return $this->getBy(array("name" => $name));
  }
}
?>