<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class JobMessagesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [JobMessage::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?JobMessage {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param Job|int $job
   * @return ICollection|JobMessage[]
   */
  public function findByJobAndSuccess($job, bool $success): ICollection {
    return $this->findBy(["job" => $job, "success" => $success]);
  }
}
?>