<?php
namespace Nexendrie\Model;

use Nextras\Orm\Collection\ICollection,
    Nexendrie\Orm\Article as ArticleEntity,
    Nexendrie\Orm\Comment as CommentEntity;

/**
 * News Model
 *
 * @author Jakub Konečný
 */
class News extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  protected $itemsPerPage;
  
  /**
   * @param int $itemsPerPage
   * @param \Nexendrie\Orm\Model $orm
   */
  function __construct($itemsPerPage, \Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
    $this->itemsPerPage = (int) $itemsPerPage;
  }
  
  /**
   * @param \Nette\Security\User $user
   */
  function setUser(\Nette\Security\User $user) {
    $this->user = $user;
  }
  
  /**
   * Show a page of news
   * 
   * @param \Nette\Utils\Paginator $paginator
   * @param int $page
   * @return \ArticleEntity[]
   */
  function page(\Nette\Utils\Paginator $paginator, $page = 1) {
    $paginator->page = $page;
    $paginator->itemsPerPage = $this->itemsPerPage;
    return $this->orm->articles->findNews()->limitBy($paginator->getLength(), $paginator->getOffset());
  }
  
  /**
   * Get list of all news
   * 
   * @return ArticleEntity[]
   */
  function all() {
    return $this->orm->articles->findNews();
  }
  
  /**
   * Show specified news
   * 
   * @param type $id
   * @return ArticleEntity
   * @throws NewsNotFoundException
   */
  function view($id) {
    $news = $this->orm->articles->getById($id);
    if(!$news) throw new NewsNotFoundException("Specified news does not exist.");
    else return $news;
  }
  
  /**
   * Get comments meeting specified rules
   * 
   * @param int $news
   * @return ArticleEntity[]
   */
  function viewComments($news = 0) {
    if($news === 0) return $this->orm->articles->findAll();
    else return $this->orm->comments->findByNews($news);
  }
  
  /**
   * Check whetever specified news exists
   * 
   * @param int $id News' id
   * @return bool
   */
  function exists($id) {
    $row = $this->orm->articles->getByID($id);
    return (bool) $row;
  }
}

class NewsNotFoundException extends RecordNotFoundException {
  
}
?>