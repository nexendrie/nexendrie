<?php
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
  function getById($id) {
    return $this->getBy(array("id" => $id));
  }
  
  /**
   * @param string $category
   * @return ICollection|Article[]
   */
  function findByCategory($category) {
    return $this->findBy(array("category" => $category));
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