<?php
namespace Nexendrie\Orm;

use Nextras\Orm\Repository\Repository,
    Nextras\Orm\Collection\ICollection;

/**
 * @author Jakub Konečný
 * @method Article[]|NULL getById($id)
 */
class ArticlesRepository extends Repository {
  /**
   * @return ICollection|Article[]
   */
  function findNews() {
    return $this->findBy(array("category" => "news"))->orderBy("added", ICollection::DESC);
  }
}
?>