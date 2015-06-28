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
  
  /**
   * Shows specified shop
   * 
   * @param int $id
   * @return array
   * @throws \Nette\Application\ForbiddenRequestException
   */
  function showShop($id) {
    $shop = $this->db->table("shops")->get($id);
    if(!$shop) throw new \Nette\Application\BadRequestException("Specified shop does not exist.");
    $return = array(
      "shop" => $shop, "items" => array()
    );
    $items = $this->db->table("items")
      ->where("shop", $id);
    foreach($items as $item) {
      $i = new \stdClass;
      foreach($item as $key => $value) {
        $i->$key = $value;
      }
      $return["items"][] = $i;
    }
    return $return;
  }
}
?>