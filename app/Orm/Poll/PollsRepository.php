<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

/**
 * @author Jakub Konečný
 */
final class PollsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Poll::class];
  }
  
  /**
   * @param int $id
   */
  public function getById($id): ?Poll {
    return $this->getBy(["id" => $id]);
  }
}
?>