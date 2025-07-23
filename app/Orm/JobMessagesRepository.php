<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method JobMessage|null getById(int $id)
 * @method JobMessage|null getBy(array $conds)
 * @method ICollection|JobMessage[] findBy(array $conds)
 * @method ICollection|JobMessage[] findAll()
 */
final class JobMessagesRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [JobMessage::class];
  }
  
  /**
   * @return ICollection|JobMessage[]
   */
  public function findByJobAndSuccess(Job|int $job, bool $success): ICollection {
    return $this->findBy(["job" => $job, "success" => $success]);
  }
}
?>