<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Shop as ShopEntity;

/**
 * Market Model
 *
 * @author Jakub Konečný
 */
class Market extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  
  /**
   * @param \Nexendrie\Orm\Model $orm
   */
  function __construct(\Nexendrie\Orm\Model $orm) {
    $this->orm = $orm;
  }
  
  /**
   * Gets list of shops
   * 
   * @return ShopEntity[]
   */
  function listOfShops() {
    return $this->orm->shops->findAll();
  }
  
  /**
   * Check whetever specified shop exists
   * 
   * @param int $id Shop's id
   * @return bool
   */
  function exists($id) {
    return (bool) $this->orm->shops->getById($id);
  }
  
  /**
   * Get specified shop's details
   * 
   * @param int $id Shop's id
   * @return ShopEntity
   * @throws ShopNotFoundException
   */
  function get($id) {
    $shop = $this->orm->shops->getById($id);
    if(!$shop) throw new ShopNotFoundException("Specified shop was not found.");
    else return $shop;
  }
  
  /**
   * Edit specified shop
   * 
   * @param int $id
   * @param array $data
   */
  function edit($id, array $data) {
    $shop = $this->orm->shops->getById($id);
    foreach($data as $key => $value) {
      $shop->$key = $value;
    }
    $this->orm->shops->persistAndFlush($shop);
  }
}

class ShopNotFoundException extends RecordNotFoundException {
  
}

class ItemNotFoundException extends RecordNotFoundException {
  
}

class WrongShopException extends AccessDeniedException {
  
}

class InsufficientFunds extends AccessDeniedException {
  
}
?>