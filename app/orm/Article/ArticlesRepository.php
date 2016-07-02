<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Article|NULL getById($id)
 * @method ICollection|Article[] findByCategory(string $category) 
 */
class ArticlesRepository extends \Nextras\Orm\Repository\Repository {
  static function getEntityClassNames() {
    return [Article::class];
  }
  /**
   * @return ICollection|Article[]
   */
  function findNews() {
    return $this->findBy(array("category" => Article::CATEGORY_NEWS))->orderBy("added", ICollection::DESC);
  }
  
  /**
   * @return ICollection|Article[]
   */
  function findChronicle() {
    return $this->findBy(array("category" => Article::CATEGORY_CHRONICLE))->orderBy("added", ICollection::DESC);
  }
}
?>