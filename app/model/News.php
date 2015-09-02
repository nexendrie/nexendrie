<?php
namespace Nexendrie\Model;

use Nextras\Orm\Collection\ICollection;

/**
 * News Model
 *
 * @author Jakub Konečný
 * @property-write int $itemsPerPage
 */
class News extends \Nette\Object {
  /** @var \Nette\Database\Context */
  protected $db;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nexendrie\Model\Profile */
  protected $profileModel;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var \Nexendrie\Model\Locale */
  protected $localeModel;
  /** @var int */
  protected $itemsPerPage = 10;
  
  /**
   * @param \Nette\Database\Context $db
   * @param \Nexendrie\Model\Profile $profileModel
   * @param \Nexendrie\Model\Locale $localeModel
   */
  function __construct(\Nette\Database\Context $db, \Nexendrie\Orm\Model $orm, \Nexendrie\Model\Profile $profileModel, \Nexendrie\Model\Locale $localeModel) {
    $this->db = $db;
    $this->orm = $orm;
    $this->profileModel = $profileModel;
    $this->localeModel = $localeModel;
  }
  
  /**
   * @param \Nette\Security\User $user
   */
  function setUser(\Nette\Security\User $user) {
    $this->user = $user;
  }
  
  /**
   * @param int $amount
   */
  function setItemsPerPage($amount) {
    if(is_int($amount)) $this->itemsPerPage = $amount;
  }
  
  /**
   * Show a page of news
   * 
   * @param \Nette\Utils\Paginator $paginator
   * @param int $page
   * @return \stdClass[]
   */
  function page(\Nette\Utils\Paginator $paginator, $page = 1) {
    $return = array();
    $paginator->page = $page;
    $paginator->itemsPerPage = $this->itemsPerPage;
    $news = $this->db->table("news")->order("added DESC")
      ->limit($paginator->getLength(), $paginator->getOffset());
    foreach($news as $new) {
      $n = new \stdClass;
      foreach($new as $key => $value) {
        if($key === "text") {
          $n->$key = substr($value, 0 , 150);
          continue;
        } elseif($key === "author") {
          $user = $this->profileModel->getNames($value);
          $n->$key = $user->publicname;
          $key .= "_username";
          $n->$key = $user->username;
        } elseif($key === "added") {
          $n->$key = $this->localeModel->formatDateTime($value);
        } else {
          $n->$key = $value;
        }
      }
      $return[] = $n;
    }
    return $return;
  }
  
  /**
   * Get list of all news
   * 
   * @return \stdClass[]
   */
  function all() {
    return $this->orm->news->findAll()->orderBy("added", ICollection::DESC);
  }
  
  /**
   * Show specified news
   * 
   * @param type $id
   * @return \stdClass
   * @throws \Nette\Application\BadRequestException
   */
  function view($id) {
    $news = $this->orm->news->getById($id);
    if(!$news) throw new \Nette\Application\BadRequestException("Specified news does not exist.");
    else return $news;
  }
  
  /**
   * Add news
   * 
   * @param \Nette\Utils\ArrayHash $data
   * @throws \Nette\Application\ForbiddenRequestException
   * @return void
   */
  function add(\Nette\Utils\ArrayHash $data) {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException ("This action requires authentication.", 401);
    if(!$this->user->isAllowed("news", "add")) throw new \Nette\Application\ForbiddenRequestException ("You don't have permissions for adding news.", 403);
    $news = new \Nexendrie\Orm\News;
    foreach($data as $key => $value) {
      $news->$key = $value;
    }
    $news->author = $this->orm->users->getById($this->user->id);
    $news->added = time();
    $this->orm->news->persistAndFlush($news);
  }
  
  /**
   * Adds comment to news
   * 
   * @param \Nette\Utils\ArrayHash $data
   * @throws \Nette\Application\ForbiddenRequestException
   * @return void
   */
  function addComment(\Nette\Utils\ArrayHash $data) {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException ("This action requires authentication.", 401);
    if(!$this->user->isAllowed("comment", "add")) throw new \Nette\Application\ForbiddenRequestException ("You don't have permissions for adding comments.", 403);
    $comment = new \Nexendrie\Orm\Comment;
    foreach($data as $key => $value) {
      $comment->$key = $value;
    }
    $comment->news = $this->orm->news->getById($data["news"]);
    $comment->author = $this->orm->users->getById($this->user->id);
    $comment->added = time();
    $this->orm->comments->persistAndFlush($comment);
  }
  
  /**
   * Get comments meeting specified rules
   * 
   * @param int $news
   * @return \Nexendrie\Orm\News[]
   */
  function viewComments($news = 0) {
    if($news === 0) return $this->orm->news->findAll();
    else return $this->orm->comments->findByNews($news);
  }
  
  /**
   * Check whetever specified news exists
   * 
   * @param int $id News' id
   * @return bool
   */
  function exists($id) {
    $row = $this->orm->news->getByID($id);
    return (bool) $row;
  }
  
  /**
   * Edit specified news
   * 
   * @param int $id News' id
   * @param \Nette\Utils\ArrayHash $data
   * @throws \Nette\Application\ForbiddenRequestException
   * @throws \Nette\ArgumentOutOfRangeException
   */
  function edit($id, \Nette\Utils\ArrayHash $data) {
    if(!$this->user->isLoggedIn()) throw new \Nette\Application\ForbiddenRequestException ("This action requires authentication.", 401);
    if(!$this->user->isAllowed("news", "edit")) throw new \Nette\Application\ForbiddenRequestException ("You don't have permissions for adding news.", 403);
    $news = $this->orm->news->getById($id);
    if(!$news) throw new \Nette\ArgumentOutOfRangeException("Specified news does not exist");
    foreach($data as $key => $value) {
      $news->$key = $value;
    }
    $this->orm->news->persistAndFlush($news);
  }
  
}
?>