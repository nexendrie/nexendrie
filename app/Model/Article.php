<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Article as ArticleEntity;
use Nexendrie\Orm\Comment as CommentEntity;
use Nexendrie\Orm\Model as ORM;
use Nextras\Orm\Collection\ICollection;

/**
 * Article Model
 *
 * @author Jakub Konečný
 */
final class Article {
  private int $itemsPerPage;
  
  public function __construct(private readonly ORM $orm, private readonly \Nette\Security\User $user, SettingsRepository $sr) {
    $this->itemsPerPage = (int) $sr->settings["pagination"]["articles"];
  }
  
  /**
   * Get all articles
   * 
   * @return ArticleEntity[]|ICollection
   */
  public function listOfArticles(): ICollection {
    return $this->orm->articles->findAll()->orderBy("created", ICollection::DESC);
  }
  
  /**
   * Get list of news
   *
   * @return ArticleEntity[]|ICollection
   */
  public function listOfNews(\Nette\Utils\Paginator $paginator = null): ICollection {
    $news = $this->orm->articles->findNews();
    if($paginator !== null) {
      $paginator->itemsPerPage = $this->itemsPerPage;
      $paginator->itemCount = $news->count();
      $news = $news->limitBy($paginator->getLength(), $paginator->getOffset());
    }
    return $news;
  }
  
  /**
   * Get list of articles from specified category
   *
   * @return ArticleEntity[]|ICollection
   */
  public function category(string $name, ?\Nette\Utils\Paginator $paginator = null): ICollection {
    $articles = $this->orm->articles->findByCategory($name);
    if($paginator !== null) {
      $paginator->itemsPerPage = $this->itemsPerPage;
      $paginator->itemCount = $articles->count();
      $articles = $articles->limitBy($paginator->getLength(), $paginator->getOffset());
    }
    return $articles;
  }
  
  /**
   * Show specified article
   *
   * @throws ArticleNotFoundException
   */
  public function view(int $id): ArticleEntity {
    $article = $this->orm->articles->getById($id);
    return $article ?? throw new ArticleNotFoundException();
  }
  
  /**
   * Get comments meeting specified rules
   *
   * @return CommentEntity[]|ICollection
   */
  public function viewComments(int $article = 0): ICollection {
    if($article === 0) {
      return $this->orm->comments->findBy(["deleted" => false, ]);
    }
    return $this->orm->comments->findByArticle($article);
  }
  
  /**
   * Add article
   *
   * @return int Id of the new article
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   */
  public function addArticle(array $data): int {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    if(!$this->user->isAllowed("article", "add")) {
      throw new MissingPermissionsException();
    }
    $article = new ArticleEntity();
    $this->orm->articles->attach($article);
    foreach($data as $key => $value) {
      $article->$key = $value;
    }
    $article->author = $this->user->id;
    $article->author->lastActive = time();
    $this->orm->articles->persistAndFlush($article);
    return $article->id;
  }
  
  /**
   * Adds comment to article
   *
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   */
  public function addComment(array $data): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException("This action requires authentication.");
    }
    if(!$this->user->isAllowed("comment", "add")) {
      throw new MissingPermissionsException("You don't have permissions for adding comments.");
    }
    $comment = new CommentEntity();
    $this->orm->comments->attach($comment);
    foreach($data as $key => $value) {
      $comment->$key = $value;
    }
    /** @var \Nexendrie\Orm\User $author */
    $author = $this->orm->users->getById($this->user->id);
    $comment->author = $author;
    $comment->author->lastActive = time();
    $this->orm->comments->persistAndFlush($comment);
  }
  
  /**
   * Edit specified article
   *
   * @throws AuthenticationNeededException
   * @throws MissingPermissionsException
   * @throws ArticleNotFoundException
   */
  public function editArticle(int $id, array $data): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException("This action requires authentication.");
    }
    $article = $this->orm->articles->getById($id);
    if($article === null) {
      throw new ArticleNotFoundException();
    }
    if(!$this->user->isAllowed("article", "edit") && $article->author->id !== $this->user->id) {
      throw new MissingPermissionsException("You don't have permissions for editting articles.");
    }
    foreach($data as $key => $value) {
      $article->$key = $value;
    }
    $this->orm->articles->persistAndFlush($article);
  }
  
  /**
   * Check whether specified article exists
   */
  public function exists(int $id): bool {
    $row = $this->orm->articles->getById($id);
    return $row !== null;
  }
}
?>