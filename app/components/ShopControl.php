<?php
namespace Nexendrie\Components;

use Nexendrie\Orm\Shop as ShopEntity,
    Nette\Application\BadRequestException;

/**
 * Shop Control
 *
 * @author Jakub Konečný
 */
class ShopControl extends \Nette\Application\UI\Control {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var ShopEntity */
  protected $shop;
  /** @var int */
  protected $id;
  
  /**
   * @param \Nexendrie\Orm\Model $orm
   * @param \Nette\Security\User $user
   */
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * @return ShopEntity
   * @throws BadRequestException
   */
  function getShop() {
    if(isset($this->shop)) return $this->shop;
    $shop = $this->orm->shops->getById($this->id);
    if(!$shop) throw new BadRequestException("Specified shop does not exist.");
    $this->shop = $shop;
  }
  
  /**
   * @param int $id
   */
  function setId($id) {
    try {
      $this->id = $id;
      $this->getShop();
    } catch(BadRequestException $e) {
      throw $e;
    }
  }
  
  /**
   * @return void
   */
  function render() {
    $template = $this->template;
    $template->setFile(__DIR__ . "/shop.latte");
    $template->shop = $this->getShop();
    $template->user = $this->user;
    $template->render();
  }
  
  function handleBuy($item) {
    
  }
}

interface ShopControlFactory {
  /** @return ShopControl */
  function create();
}
?>