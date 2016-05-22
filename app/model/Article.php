<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Article as ArticleEntity,
    Nexendrie\Orm\Comment as CommentEntity,
    Nextras\Orm\Collection\ICollection;

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
   * Get all articles
   * 
   * @return ArticleEntity[]
   */
  function listOfArticles() {
    return $this->orm->articles->findAll()->orderBy("added", ICollection::DESC);
  }
  
  /**
   * Get list of news
   * 
   * @param \Nette\Utils\Paginator $paginator
   * @return ArticleEntity[]
   */
  function listOfNews(\Nette\Utils\Paginator $paginator = NULL) {
    $news = $this->orm->articles->findNews();
    if($paginator) {
      $paginator->itemsPerPage = $this->itemsPerPage;
      $news = $news->limitBy($paginator->getLength(), $paginator->getOffset());
    }
    return $news;
  }
  
  /**
   * Get list of articles from specified category
   * 
   * @param string $name
   * @param \Nette\Utils\Paginator $paginator
   * @return ArticleEntity[]
   */
  function category($name, \Nette\Utils\Paginator $paginator = NULL) {
    $articles = $this->orm->articles->findByCategory($name);
    if($paginator) {
      $paginator->itemsPerPage = $this->itemsPerPage;
      $articles = $articles->limitBy($paginator->getLength(), $paginator->getOffset());
    }
    return $articles;
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
    if(!$article) throw new ArticleNotFoundException;
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
   * Add article
   * 
   * @param array $data
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @return void
   */
  function addArticle(array $data) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    if(!$this->user->isAllowed("article", "add")) throw new MissingPermissionsException;
    $article = new ArticleEntity;
    $this->orm->articles->attach($article);
    foreach($data as $key => $value) {
      $article->$key = $value;
    }
    $article->author = $this->user->id;
    $article->author->lastActive = $article->added = time();
    $this->orm->articles->persistAndFlush($article);
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
    $comment->author = $this->orm->users->getById($this->user->id);
    $comment->author->lastActive = $comment->added = time();
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
    $article = $this->orm->articles->getById($id);
    if(!$article) throw new ArticleNotFoundException;
    if(!$this->user->isAllowed("article", "edit") AND $article->author->id != $this->user->id) throw new MissingPermissionsException("You don't have permissions for editting articles.");
    foreach($data as $key => $value) {
      $article->$key = $value;
    }
    $this->orm->articles->persistAndFlush($article);
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