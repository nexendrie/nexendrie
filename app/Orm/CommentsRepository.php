<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Comment|null getById(int $id)
 * @method Comment|null getBy(array $conds)
 * @method ICollection|Comment[] findBy(array $conds)
 * @method ICollection|Comment[] findAll()
 */
final class CommentsRepository extends \Nextras\Orm\Repository\Repository {
  public static function getEntityClassNames(): array {
    return [Comment::class];
  }
  
  /**
   * @param Article|int $article
   * @return ICollection|Comment[]
   */
  public function findByArticle($article): ICollection {
    return $this->findBy(["article" => $article, "deleted" => false,]);
  }
  
  /**
   * @param User|int $author
   * @return ICollection|Comment[]
   */
  public function findByAuthor($author): ICollection {
    return $this->findBy(["author" => $author, "deleted" => false,]);
  }
}
?>