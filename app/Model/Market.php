<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Shop as ShopEntity,
    Nexendrie\Orm\Item as ItemEntity,
    Nexendrie\Orm\UserItem as UserItemEntity,
    Nextras\Orm\Collection\ICollection;

/**
 * Market Model
 *
 * @author Jakub Konečný
 */
class Market {
  /** @var Events */
  protected $eventsModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  use \Nette\SmartObject;
  
  /**
   * @param \Nexendrie\Model\Events $eventsModel
   * @param \Nexendrie\Orm\Model $orm
   * @param \Nette\Security\User $user
   */
  function __construct(Events $eventsModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->eventsModel = $eventsModel;
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Gets list of shops
   * 
   * @return ShopEntity[]|ICollection
   */
  function listOfShops(): ICollection {
    return $this->orm->shops->findAll();
  }
  
  /**
   * Get list of items
   * 
   * @return ItemEntity[]|ICollection
   */
  function listOfItems(): ICollection {
    return $this->orm->items->findAll();
  }
  
  /**
   * Check whetever specified shop exists
   * 
   * @param int $id Shop's id
   * @return bool
   */
  function exists(int $id): bool {
    return (bool) $this->orm->shops->getById($id);
  }
  
  /**
   * Get specified shop's details
   * 
   * @param int $id Shop's id
   * @return ShopEntity
   * @throws ShopNotFoundException
   */
  function getShop(int $id): ShopEntity {
    $shop = $this->orm->shops->getById($id);
    if(!$shop) {
      throw new ShopNotFoundException("Specified shop was not found.");
    } else {
      return $shop;
    }
  }
  
  /**
   * Edit specified shop
   * 
   * @param int $id
   * @param array $data
   * @return void
   */
  function editShop(int $id, array $data) {
    $shop = $this->orm->shops->getById($id);
    foreach($data as $key => $value) {
      $shop->$key = $value;
    }
    $this->orm->shops->persistAndFlush($shop);
  }
  
  /**
   * Add new shop
   * 
   * @param array $data
   * @return void
   */
  function addShop(array $data) {
    $shop = new ShopEntity;
    foreach($data as $key => $value) {
      $shop->$key = $value;
    }
    $this->orm->shops->persistAndFlush($shop);
  }
  
  /**
   * Get specified item's details
   * 
   * @param int $id
   * @return ItemEntity
   * @throws ItemNotFoundException
   */
  function getItem(int $id): ItemEntity {
    $item = $this->orm->items->getById($id);
    if(!$item) {
      throw new ItemNotFoundException("Specified item was not found.");
    } else {
      return $item;
    }
  }
  
  /**
   * Edit specified item
   * 
   * @param int $id
   * @param array $data
   * @return void
   */
  function editItem(int $id, array $data) {
    $item = $this->orm->items->getById($id);
    foreach($data as $key => $value) {
      $item->$key = $value;
    }
    $this->orm->items->persistAndFlush($item);
  }
  
  /**
   * Add new item
   * 
   * @param array $data
   * @return void
   */
  function addItem(array $data) {
    $item = new ItemEntity;
    $this->orm->items->attach($item);
    foreach($data as $key => $value) {
      $item->$key = $value;
    }
    $this->orm->items->persistAndFlush($item);
  }
  
  /**
   * @param int $item
   * @param int $shop  
   * @return void
   * @throws AuthenticationNeededException
   * @throws ItemNotFoundException
   * @throws WrongShopException
   * @throws InsufficientFundsException
   */
  function buy(int $item, int $shop) {
    $itemRow = $this->orm->items->getById($item);
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException;
    }
    if(!$itemRow) {
      throw new ItemNotFoundException("Specified item does not exist.");
    }
    if($itemRow->shop->id != $shop) {
      throw new WrongShopException;
    }
    $user = $this->orm->users->getById($this->user->id);
    if($user->money < $itemRow->price) {
      throw new InsufficientFundsException;
    }
    $row = $this->orm->userItems->getByUserAndItem($user->id, $item);
    if(!$row OR in_array($itemRow->type, ItemEntity::getEquipmentTypes())) {
      $row = new UserItemEntity;
      $row->user = $user;
      $row->item = $item;
    } else {
      $row->amount++;
    }
    $price = $itemRow->price;
    $price -= $this->eventsModel->calculateShoppingDiscount($price);
    $row->user->money = $user->money - $price;
    $row->user->lastActive = time();
    $this->orm->userItems->persistAndFlush($row);
  }
}
?>