<?php
namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class PollsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Poll::class];
  }
  
  /**
   * @param int $id
   * @return Poll|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
}
?>