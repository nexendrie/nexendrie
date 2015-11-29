<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Article|NULL getById($id)
 */
class ArticlesRepository extends \Nextras\Orm\Repository\Repository {
  /**
   * @return ICollection|Article[]
   */
  function findNews() {
    return $this->findBy(array("category" => "news"))->orderBy("added", ICollection::DESC);
  }
  
  /**
   * @return ICollection|Article[]
   */
  function findChronicle() {
    return $this->findBy(array("category" => "chronicle"))->orderBy("added", ICollection::DESC);
  }
}
?>