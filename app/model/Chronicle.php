<?php
namespace Nexendrie\Model;

/**
 * Chronicle Model
 *
 * @author Jakub Konečný
 */
class Chronicle extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * Get list of chronicle records
   * 
   * @param \Nette\Utils\Paginator $paginator
   * @return ArticleEntity[]
   */
  function articles(\Nette\Utils\Paginator $paginator = NULL) {
    $articles = $this->orm->articles->findChronicle();
    if($paginator) {
      $paginator->itemsPerPage = $this->itemsPerPage;
      $articles = $articles->limitBy($paginator->getLength(), $paginator->getOffset());
    }
    return $articles;
  }
  
}
?>