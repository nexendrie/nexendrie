<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class CommentsRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Comment::class];
  }
  
  /**
   * @param int $id
   * @return Comment|NULL
   */
  function getById($id): ?Comment {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param Article|int $article
   * @return ICollection|Comment[]
   */
  function findByArticle($article): ICollection {
    return $this->findBy(["article" => $article]);
  }
}
?>