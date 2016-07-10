<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class MountTypesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [MountType::class];
  }
  
  /**
   * @param int $id
   * @return MountType|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
}
?>