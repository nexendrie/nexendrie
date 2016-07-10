<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class ShopsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Shop::class];
  }
  
  /**
   * @param int $id
   * @return Shop|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
}
?>