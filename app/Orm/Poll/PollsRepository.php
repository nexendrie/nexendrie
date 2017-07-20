<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
class PollsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames(): array {
    return [Poll::class];
  }
  
  /**
   * @param int $id
   * @return Poll|NULL
   */
  public function getById($id): ?Poll {
    return $this->getBy(["id" => $id]);
  }
}
?>