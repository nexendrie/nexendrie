<?php
namespace Nexendrie;

/**
 * Polls Model
 *
 * @author Jakub Konečný
 */
class Polls extends \Nette\Object {
  /** @var \Nette\Database\Context */
  protected $db;
  /** @var \Nexendrie\Profile */
  protected $profileModel;
  /** @var \Nexendrie\Locale */
  protected $localeModel;
  
  /**
   * @param \Nette\Database\Context $db
   * @param \Nexendrie\Profile $profileModel
   * @param \Nexendrie\Locale $localeModel
   */
  function __construct(\Nette\Database\Context $db, \Nexendrie\Profile $profileModel, \Nexendrie\Locale $localeModel) {
    $this->db = $db;
    $this->profileModel = $profileModel;
    $this->localeModel = $localeModel;
  }
  
  /**
   * Get list of all polls
   * 
   * @return array
   */
  function all() {
    $return = array();
    $polls = $this->db->table("polls")->order("added DESC");
    foreach($polls as $poll) {
      $p = new \stdClass;
      foreach($poll as $key => $value) {
        if($key === "text") {
          $p->$key = substr($value, 0 , 150);
          continue;
        } elseif($key === "author") {
          $user = $this->profileModel->getNames($value);
          $p->$key = $user->publicname;
          $key .= "_username";
          $p->$key = $user->username;
        } elseif($key === "added") {
          $p->$key = $this->localeModel->formatDateTime($value);
        } else {
          $p->$key = $value;
        }
      }
      $return[] = $p;
    }
    return $return;
  }
}
?>