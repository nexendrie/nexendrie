<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Town as TownEntity;

/**
 * Town Model
 *
 * @author Jakub Konečný
 */
class Town extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Get specified town
   * 
   * @param int $id Town's id
   * @return TownEntity
   * @throws TownNotFoundException
   */
  function get($id) {
    $town = $this->orm->towns->getById($id);
    if(!$town) throw new TownNotFoundException;
    else return $town;
  }
  
  /**
   * Get list of all towns
   * 
   * @return TownEntity[]
   */
  function listOfTowns() {
    return $this->orm->towns->findAll();
  }
  
  /**
   * Add new town
   * 
   * @param array $data
   * @return void
   */
  function add(array $data) {
    $town = new TownEntity;
    $this->orm->towns->attach($town);
    foreach($data as $key => $value) {
      $town->$key = $value;
    }
    $this->orm->towns->persistAndFlush($town);
  }
  
  /**
   * Edit specified town
   * 
   * @param int $id Town's id
   * @param array $data
   * @return void
   * @throws TownNotFoundException
   */
  function edit($id, array $data) {
    try {
      $town = $this->get($id);
    } catch(TownNotFoundException $e) {
      throw $e;
    }
    foreach($data as $key => $value) {
      $town->$key = $value;
    }
    $this->orm->towns->persistAndFlush($town);
  }
  
  /**
   * @return TownEntity[]
   */
  function townsOnSale() {
    return $this->orm->towns->findOnMarket();
  }
  
  /**
   * Buy specified mount
   * 
   * @param int $id Mount's id
   * @return void
   * @throws AuthenticationNeededException
   * @throws TownNotFoundException
   * @throws TownNotOnSaleException
   * @throws CannotBuyOwnTownException
   * @throws InsufficientLevelForTownException
   * @throws InsufficientFunds
   */
  function buy($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $town = $this->orm->towns->getById($id);
    if(!$town) throw new TownNotFoundException;
    if(!$town->onMarket) throw new TownNotOnSaleException;
    if($town->owner->id === $this->user->id) throw new CannotBuyOwnTownException;
    $user = $this->orm->users->getById($this->user->id);
    if($user->group->level < 350) throw new InsufficientLevelForTownException;
    if($user->money < $town->price) throw new InsufficientFunds;
    $seller = $town->owner;
    $seller->money += $town->price;
    $user->money -= $town->price;
    $town->owner = $user;
    $town->onMarket = false;
    $this->orm->towns->persist($town);
    $this->orm->users->persist($seller);
    $this->orm->flush();
  }
}

class TownNotFoundException extends RecordNotFoundException {
  
}

class TownNotOnSaleException extends AccessDeniedException {
  
}

class InsufficientLevelForTownException extends AccessDeniedException {
  
}

class CannotBuyOwnTownException extends AccessDeniedException {
  
}
?>