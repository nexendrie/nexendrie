<?php
namespace Nexendrie;

/**
 * Market Model
 *
 * @author Jakub Konečný
 */
class Market extends \Nette\Object {
  /** @var \Nette\Database\Context */
  protected $db;
  
  /**
   * @param \Nette\Database\Context $db
   */
  function __construct(\Nette\Database\Context $db) {
    $this->db = $db;
  }
  
  /**
   * Gets list of shops
   * 
   * @return array
   */
  function listOfShops() {
    $return = array();
    $shops = $this->db->table("shops");
    foreach($shops as $shop) {
      $s = new \stdClass;
      foreach($shop as $key => $value) {
        $s->$key = $value;
      }
      $return[] = $s;
    }
    return $return;
  }
}
?>