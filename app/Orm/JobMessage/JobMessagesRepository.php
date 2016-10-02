<?php
declare(strict_types=1);

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
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param Job|int $job
   * @param bool $success
   * @return ICollection|JobMessage[]
   */
  function findByJobAndSuccess($job, bool $success): ICollection {
    return $this->findBy(["job" => $job, "success" => $success]);
  }
}
?>