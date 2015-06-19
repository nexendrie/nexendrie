<?php
namespace Nexendrie;

use Nette\Utils\Arrays;

/**
 * News Model
 *
 * @author Jakub Konečný
 * @property-write int $itemsPerPage
 */
class News extends \Nette\Object {
  /** @var \Nette\Database\Context */
  protected $db;
  /** @var int */
  protected $itemsPerPage = 10;
  /** @var array */
  protected $names = array();
  
  function __construct(\Nette\Database\Context $db) {
    $this->db = $db;
  }
  
  /**
   * @param int $amount
   * @return void
   */
  function setItemsPerPage($amount) {
    if(is_int($amount)) $this->itemsPerPage = $amount;
  }
  
  function getNames($id) {
    $user = Arrays::get($this->names, $id, false);
    if(!$user) {
      $user = $this->db->table("users")->get($id);
      $this->names[$id] = (object) array(
        "username" => $user->username, "publicname" => $user->publicname
      );
    }
    return $user;
  }
  
  /**
   * @param string $date
   * @return string
   */
  function formatDate($date) {
    $day = (int) substr($date, 8, 2);
    $month = (int) substr($date, 5, 2);
    $year = substr($date, 0, 4);
    $time = substr($date, 10, 8);
    return "$day.$month.$year $time";
  }
  
  /**
   * 
   * 
   * @param \Nette\Utils\Paginator $paginator
   * @param int $page
   * @return array
   */
  function page(\Nette\Utils\Paginator $paginator, $page = 1) {
    $return = $users = array();
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
          $user = $this->getNames($value);
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
        $user = $this->getNames($value);
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
}
?>