<?php
namespace Nexendrie;

/**
 * News Model
 *
 * @author Jakub Konečný
 * @property-write int $itemsPerPage
 */
class News extends \Nette\Object {
  /** @var \Nette\Database\Context */
  protected $db;
  /** @var \Nexendrie\Profile */
  protected $profileModel;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  protected $itemsPerPage = 10;
  
  /**
   * @param \Nette\Database\Context $db
   * @param \Nexendrie\Profile $profileModel
   */
  function __construct(\Nette\Database\Context $db, \Nexendrie\Profile $profileModel) {
    $this->db = $db;
    $this->profileModel = $profileModel;
  }
  
  /**
   * @param \Nette\Security\User $user
   * @return void
   */
  function setUser(\Nette\Security\User $user) {
    $this->user = $user;
  }
  
  /**
   * @param int $amount
   * @return void
   */
  function setItemsPerPage($amount) {
    if(is_int($amount)) $this->itemsPerPage = $amount;
  }
  
  /**
   * Formats date of news (czech locale)
   * 
   * @param string $date
   * @return string
   */
  protected function formatDate($date) {
    $day = (int) substr($date, 8, 2);
    $month = (int) substr($date, 5, 2);
    $year = substr($date, 0, 4);
    $time = substr($date, 10, 6);
    return "$day.$month.$year $time";
  }
  
  /**
   * Show a page of news
   * 
   * @param \Nette\Utils\Paginator $paginator
   * @param int $page
   * @return array
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
          continue;
        } elseif($key === "author") {
          $user = $this->profileModel->getNames($value);
          $n->$key = $user->publicname;
          $key .= "_username";
          $n->$key = $user->username;
        } elseif($key === "added") {
          $n->$key = $this->formatDate($value);
        } else {
          $n->$key = $value;
        }
      }
      $return[] = $n;
    }
    return $return;
  }
  
  /**
   * Show specified news
   * 
   * @param type $id
   * @return \stdClass|boolean
   */
  function view($id) {
    $new = $this->db->table("news")->get($id);
    if(!$new) return false;
    $return = new \stdClass;
    foreach($new as $key => $value) {
      if($key === "author") {
        $user = $this->profileModel->getNames($value);
        $return->$key = $user->publicname;
        $key .= "_username";
        $return->$key = $user->username;
      } elseif($key === "added") {
        $return->$key = $this->formatDate($value);
      } else {
        $return->$key = $value;
      }
    }
    return $return;
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
    $data["author"] = $this->user->id;
    $this->db->query("INSERT INTO news", $data);
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
    $data["author"] = $this->user->id;
    $this->db->query("INSERT INTO comments", $data);
  }
  
  /**
   * Get comments meeting specified rules
   * 
   * @param int $news
   * @return array
   */
  function viewComments($news = 0) {
    $return = array();
    $comments = $this->db->table("comments");
    if($news > 0) $comments->where("news", $news);
    foreach($comments as $comment) {
      $n = new \stdClass;
      foreach($comment as $key => $value) {
        if($key === "author") {
          $user = $this->profileModel->getNames($value);
          $n->$key = $user->publicname;
          $key .= "_username";
          $n->$key = $user->username;
        } elseif($key === "added") {
          $n->$key = $this->formatDate($value);
        } else {
          $n->$key = $value;
        }
      }
      $return[] = $n;
    }
    return $return;
  }
}
?>