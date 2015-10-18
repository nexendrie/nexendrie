<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Article as ArticleEntity,
    Nexendrie\Orm\Comment as CommentEntity;

/**
 * Article Model
 *
 * @author Jakub Konečný
 */
class Article extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  protected $itemsPerPage;
  
  function __construct($itemsPerPage, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
    $this->itemsPerPage = (int) $itemsPerPage;
  }
  
  /**
   * Get list of news
   * 
   * @param \Nette\Utils\Paginator $paginator
   * @return ArticleEntity[]
   */
  function listOfNews(\Nette\Utils\Paginator $paginator = NULL) {
    if($paginator) $paginator->itemsPerPage = $this->itemsPerPage;
    $news = $this->orm->articles->findNews();
    if($paginator) $news->limitBy($paginator->getLength(), $paginator->getOffset());
    return $news;
  }
  
  /**
   * Show specified article
   * 
   * @param int $id
   * @return ArticleEntity
   * @throws ArticleNotFoundException
   */
  function view($id) {
    $article = $this->orm->articles->getById($id);
    if(!$article) throw new ArticleNotFound;
    else return $article;
  }
  
  /**
   * Get comments meeting specified rules
   * 
   * @param int $article
   * @return ArticleEntity[]
   */
  function viewComments($article = 0) {
    if($article === 0) return $this->orm->comments->findAll();
    else return $this->orm->comments->findByArticle($article);
  }
  
  /**
   * @param array $data
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @return void
   * @deprecated
   */
  function addNews(array $data) {
    $data["category"] = ArticleEntity::CATEGORY_NEWS;
    try {
      $this->addArticle($data);
    } catch(Exception $e) {
      throw $e;
    }
  }
  
  /**
   * Add article
   * 
   * @param array $data
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @return void
   */
  function addArticle(array $data) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    if(!$this->user->isAllowed("news", "add")) throw new MissingPermissionsException;
    $news = new ArticleEntity;
    $this->orm->articles->attach($news);
    foreach($data as $key => $value) {
      $news->$key = $value;
    }
    $news->author = $this->user->id;
    $news->added = time();
    $this->orm->articles->persistAndFlush($news);
  }
  
  /**
   * Adds comment to article
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
   * Edit specified article
   * 
   * @param int $id Article' id
   * @param array $data
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws ArticleNotFoundException
   */
  function editArticle($id, array $data) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException("This action requires authentication.");
    if(!$this->user->isAllowed("news", "edit")) throw new MissingPermissionsException("You don't have permissions for adding news.");
    $news = $this->orm->articles->getById($id);
    if(!$news) throw new ArticleNotFoundException;
    foreach($data as $key => $value) {
      $news->$key = $value;
    }
    $this->orm->articles->persistAndFlush($news);
  }
  
  /**
   * Check whetever specified article exists
   * 
   * @param int $id News' id
   * @return bool
   */
  function exists($id) {
    $row = $this->orm->articles->getByID($id);
    return (bool) $row;
  }
}

class ArticleNotFoundException extends RecordNotFoundException {
  
}
?>