<?php
namespace Nexendrie\Components;

use Nexendrie\Orm\Shop as ShopEntity,
    Nexendrie\Model\ShopNotFoundException,
    Nexendrie\Model\ItemNotFoundException,
    Nexendrie\Model\WrongShopException,
    Nexendrie\Model\AuthenticationNeededException,
    Nexendrie\Model\InsufficientFunds,
    Nexendrie\Orm\UserItem as UserItemEntity;

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
   * @throws ShopNotFoundException
   */
  function getShop() {
    if(isset($this->shop)) return $this->shop;
    $shop = $this->orm->shops->getById($this->id);
    if(!$shop) throw new ShopNotFoundException("Specified shop does not exist.");
    $this->shop = $shop;
  }
  
  /**
   * @param int $id
   */
  function setId($id) {
    try {
      $this->id = $id;
      $this->getShop();
    } catch(ShopNotFoundException $e) {
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
  
  /**
   * @param int $item
   * @return void
   * @throws AuthenticationNeededException
   * @throws ItemNotFoundException
   * @throws WrongShopException
   * @throws InsufficientFunds
   */
  protected function buy($item) {
    $itemRow = $this->orm->items->getById($item);
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    if(!$itemRow) throw new ItemNotFoundException("Specified item does not exist.");
    if($itemRow->shop->id != $this->shop->id) throw new WrongShopException("Specified item is not in current shop.");
    $user = $this->orm->users->getById($this->user->id);
    if($user->money < $itemRow->price) throw new InsufficientFunds("You do not have enough money to buy this item.");
    $row = $this->orm->userItems->getByUserAndItem($user->id, $item);
    if(!$row) {
      $row = new UserItemEntity;
      $row->user = $user;
      $row->item = $item;
    } else {
      $row->amount++;
    }
    $user->money = $user->money - $itemRow->price;
    $this->orm->userItems->persist($row);
    $this->orm->users->persist($user);
    $this->orm->flush();
  }
  
  function handleBuy($item) {
    try {
      $this->buy($item);
      $this->presenter->flashMessage("Věc koupena.");
    } catch(AuthenticationNeededException $e) {
      $this->presenter->flashMessage("Pro nákup musíš být přihlášený.");
    } catch(ItemNotFoundException $e) {
      $this->presenter->flashMessage("Zadaná věc neexistuje.");
    } catch(WrongShopException $e) {
      $this->presenter->flashMessage("Zadaná věc není v aktuálním obchodě.");
    } catch(InsufficientFunds $e) {
      $this->presenter->flashMessage("Nemáš dostatek peněz na zakoupení této věci.");
    }
  }
}

interface ShopControlFactory {
  /** @return ShopControl */
  function create();
}
?>