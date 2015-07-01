<?php
namespace Nexendrie;

/**
 * Shop Control
 *
 * @author Jakub Konečný
 */
class ShopControl extends \Nette\Application\UI\Control {
  /** @var \Nette\Database\Context */
  protected $db;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var \Nexendrie\ILocale */
  protected $localeModel;
  /** @var \stdClass */
  protected $shop;
  /** @var int */
  protected $id;
  
  function __construct(\Nette\Database\Context $db, \Nette\Security\User $user, \Nexendrie\ILocale $localeModel) {
    $this->db = $db;
    $this->user = $user;
    $this->localeModel = $localeModel;
  }
  
  function getShop() {
    if(isset($this->shop)) return $this->shop;
    $shop = $this->db->table("shops")->get($this->id);
    if(!$shop) throw new \Nette\Application\BadRequestException("Specified shop does not exist.");
    $s = new \stdClass;
    foreach($shop as $key => $value) {
      $s->$key = $value;
    }
    $this->shop = $s;
  }
  
  /**
   * @param int $id
   */
  function setId($id) {
    try {
      $this->id = $id;
      $this->getShop();
    } catch(\Nette\Application\BadRequestException $e) {
      throw $e;
    }
  }
  
  /**
   * @return array
   */
  function getItems() {
    $items = $this->db->table("items")
      ->where("shop", $this->id);
    $return = array();
    foreach($items as $item) {
      $i = new \stdClass;
      foreach($item as $key => $value) {
        if($key === "price") $value = $this->localeModel->money($item->price);;
        $i->$key = $value;
      }
      $return[] = $i;
    }
    return $return;
  }
  
  /**
   * @return void
   */
  function render() {
    $template = $this->template;
    $template->setFile(__DIR__ . "/shop.latte");
    $template->shop = $this->getShop();
    $template->items = $this->getItems();
    $template->user = $this->user;
    $template->render();
  }
}
?>