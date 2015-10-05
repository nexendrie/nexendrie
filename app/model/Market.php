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
}

class ShopNotFoundException extends RecordNotFoundException {
  
}
?>