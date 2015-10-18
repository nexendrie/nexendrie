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
   * Add news
   * 
   * @param array $data
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @return void
   */
  function add(array $data) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    if(!$this->user->isAllowed("news", "add")) throw new MissingPermissionsException;
    $news = new ArticleEntity;
    $this->orm->articles->attach($news);
    foreach($data as $key => $value) {
      $news->$key = $value;
    }
    $news->author = $this->user->id;
    $news->added = time();
    $news->category = ArticleEntity::CATEGORY_NEWS;
    $this->orm->articles->persistAndFlush($news);
  }
  
  /**
   * Adds comment to news
   * 
   * @param array $data
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @return void
   */
  function addComment(array $data) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException("This action requires authentication.");
    if(!$this->user->isAllowed("comment", "add")) throw new MissingPermissionsException("You don't have permissions for adding comments.");
    $comment = new CommentEntity;
    $this->orm->comments->attach($comment);
    foreach($data as $key => $value) {
      $comment->$key = $value;
    }
    $comment->author = $this->user->id;
    $comment->added = time();
    $this->orm->comments->persistAndFlush($comment);
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
  
  /**
   * Edit specified news
   * 
   * @param int $id News' id
   * @param array $data
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws NewsNotFoundException
   */
  function edit($id, array $data) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException("This action requires authentication.");
    if(!$this->user->isAllowed("news", "edit")) throw new MissingPermissionsException("You don't have permissions for adding news.");
    $news = $this->orm->articles->getById($id);
    if(!$news) throw new NewsNotFoundException("Specified news does not exist");
    foreach($data as $key => $value) {
      $news->$key = $value;
    }
    $this->orm->articles->persistAndFlush($news);
  }
}

class NewsNotFoundException extends RecordNotFoundException {
  
}
?>