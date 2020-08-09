<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

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
  
  /**
   * @param User|int $author
   * @return ICollection|Poll[]
   */
  public function findByAuthor($author): ICollection {
    return $this->findBy(["author" => $author]);
  }
}
?>