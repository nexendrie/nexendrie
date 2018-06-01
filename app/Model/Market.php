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
final class Market {
  /** @var Events */
  protected $eventsModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
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
    if(is_null($shop)) {
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
    if(is_null($item)) {
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
    if(is_null($itemRow)) {
      throw new ItemNotFoundException("Specified item does not exist.");
    }
    if($itemRow->shop->id != $shop) {
      throw new WrongShopException();
    }
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->money < $itemRow->price) {
      throw new InsufficientFundsException();
    }
    /** @var UserItemEntity $row */
    $row = $this->orm->userItems->getByUserAndItem($user->id, $item);
    if(is_null($row) OR in_array($itemRow->type, ItemEntity::getEquipmentTypes(), true)) {
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