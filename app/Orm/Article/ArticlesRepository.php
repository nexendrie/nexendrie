<?php
declare(strict_types=1);

namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 */
class ArticlesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Article::class];
  }
  
  /**
   * @param int $id
   * @return Article|NULL
   */
  function getById($id): ?Article {
    return $this->getBy(["id" => $id]);
  }
  
  /**
   * @param string $category
   * @return ICollection|Article[]
   */
  function findByCategory(string $category): ICollection {
    return $this->findBy(["category" => $category]);
  }
  
  /**
   * @return ICollection|Article[]
   */
  function findNews(): ICollection {
    return $this->findBy(["category" => Article::CATEGORY_NEWS])->orderBy("added", ICollection::DESC);
  }
  
  /**
   * @return ICollection|Article[]
   */
  function findChronicle(): ICollection {
    return $this->findBy(["category" => Article::CATEGORY_CHRONICLE])->orderBy("added", ICollection::DESC);
  }
}
?>