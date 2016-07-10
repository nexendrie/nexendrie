<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class JobMessagesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [JobMessage::class];
  }
  
  /**
   * @param int $id
   * @return JobMessage|NULL
   */
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * @param Job|int $job
   * @param bool $success
   * @return ICollection|JobMessage[]
   */
  function findByJobAndSuccess($job, $success) {
    return $this->findBy(array("job" => $job, "success" => $success));
  }
}
?>