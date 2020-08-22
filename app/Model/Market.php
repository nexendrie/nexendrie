<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Shop as ShopEntity;
use Nexendrie\Orm\Item as ItemEntity;
use Nexendrie\Orm\UserItem as UserItemEntity;
use Nextras\Orm\Collection\ICollection;

/**
 * Market Model
 *
 * @author Jakub Konečný
 */
final class Market {
  protected Events $eventsModel;
  protected \Nexendrie\Orm\Model $orm;
  protected \Nette\Security\User $user;
  
  use \Nette\SmartObject;
  
  public function __construct(Events $eventsModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->eventsModel = $eventsModel;
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Gets list of shops
   * 
   * @return ShopEntity[]|ICollection
   */
  public function listOfShops(): ICollection {
    return $this->orm->shops->findAll();
  }
  
  /**
   * Get list of items
   * 
   * @return ItemEntity[]|ICollection
   */
  public function listOfItems(): ICollection {
    return $this->orm->items->findAll();
  }
  
  /**
   * Check whether specified shop exists
   */
  public function exists(int $id): bool {
    return (bool) $this->orm->shops->getById($id);
  }
  
  /**
   * Get specified shop's details
   *
   * @throws ShopNotFoundException
   */
  public function getShop(int $id): ShopEntity {
    $shop = $this->orm->shops->getById($id);
    if($shop === null) {
      throw new ShopNotFoundException("Specified shop was not found.");
    }
    return $shop;
  }
  
  /**
   * Edit specified shop
   *
   * @throws ShopNotFoundException
   */
  public function editShop(int $id, array $data): void {
    try {
      $shop = $this->getShop($id);
    } catch(ShopNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      $shop->$key = $value;
    }
    $this->orm->shops->persistAndFlush($shop);
  }
  
  /**
   * Add new shop
   */
  public function addShop(array $data): void {
    $shop = new ShopEntity();
    foreach($data as $key => $value) {
      $shop->$key = $value;
    }
    $this->orm->shops->persistAndFlush($shop);
  }
  
  /**
   * Get specified item's details
   *
   * @throws ItemNotFoundException
   */
  public function getItem(int $id): ItemEntity {
    $item = $this->orm->items->getById($id);
    if($item === null) {
      throw new ItemNotFoundException("Specified item was not found.");
    }
    return $item;
  }
  
  /**
   * Edit specified item
   *
   * @throws ItemNotFoundException
   */
  public function editItem(int $id, array $data): void {
    try {
      $item = $this->getItem($id);
    } catch(ItemNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      $item->$key = $value;
    }
    $this->orm->items->persistAndFlush($item);
  }
  
  /**
   * Add new item
   */
  public function addItem(array $data): void {
    $item = new ItemEntity();
    $this->orm->items->attach($item);
    foreach($data as $key => $value) {
      $item->$key = $value;
    }
    $this->orm->items->persistAndFlush($item);
  }
  
  /**
   * @throws AuthenticationNeededException
   * @throws ItemNotFoundException
   * @throws WrongShopException
   * @throws InsufficientFundsException
   */
  public function buy(int $item, int $shop): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $itemRow = $this->orm->items->getById($item);
    if($itemRow === null) {
      throw new ItemNotFoundException("Specified item does not exist.");
    }
    if($itemRow->shop->id !== $shop) {
      throw new WrongShopException();
    }
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->money < $itemRow->price) {
      throw new InsufficientFundsException();
    }
    $row = $this->orm->userItems->getByUserAndItem($user->id, $item);
    if($row === null || in_array($itemRow->type, ItemEntity::getEquipmentTypes(), true)) {
      $row = new UserItemEntity();
      $row->user = $user;
      $row->item = $item;
      $row->amount = 0;
    }
    $row->amount++;
    $price = $itemRow->price;
    $price -= $this->eventsModel->calculateShoppingDiscount($price);
    $row->user->money = $user->money - $price;
    $row->user->lastActive = time();
    $this->orm->userItems->persistAndFlush($row);
  }
}
?>