<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Order as OrderEntity;

/**
 * Order Model
 *
 * @author Jakub Konečný
 */
class Order {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get list of orders
   * 
   * @return OrderEntity[]
   */
  function listOfOrders() {
    return $this->orm->orders->findAll();
  }
}
?>