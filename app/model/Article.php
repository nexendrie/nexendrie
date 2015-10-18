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
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Show specified article
   * 
   * @param int $id
   * @return ArticleEntity
   * @throws ArticleNotFound
   */
  function view($id) {
    $article = $this->orm->articles->getById($id);
    if(!$article) throw new ArticleNotFound;
    else return $article;
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
  
}

class ArticleNotFoundException extends RecordNotFoundException {
  
}
?>