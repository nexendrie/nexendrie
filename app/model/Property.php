<?php
namespace Nexendrie\Model;

/**
 * Property Model
 *
 * @author Jakub Konečný
 */
class Property extends \Nette\Object {
  /** @var \Nexendrie\Model\Job*/
  protected $jobModel;
  /** @var \Nexendrie\Model\Bank */
  protected $bankModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Model\Job $jobModel, \Nexendrie\Model\Bank $bankModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->jobModel = $jobModel;
    $this->bankModel = $bankModel;
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Show user's possessions
   * 
   * @return array
   * @throws AuthenticationNeededException
   */
  function show() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $return = array();
    $user = $this->orm->users->getById($this->user->id);
    $return["money"] = $user->moneyT;
    $return["items"] = $user->items->get()->findBy(array("this->item->type" => "item"));
    $return["isLord"] = ($user->group->level >= 350);
    $return["towns"] = $user->ownedTowns;
    return $return;
  }
  
  /**
   * Show user's budget
   * 
   * @return array
   * @throws AuthenticationNeededException
   */
  function budget() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $budget = array(
      "incomes" => array(
        "work" => $this->jobModel->calculateMonthJobIncome(),
        "adventures" => 0,
        "taxes" => 0
      ),
      "expenses" => array(
        "incomeTax" => 0,
        "loansInterest" => 0
    ));
    $loans = $this->orm->loans->findReturnedThisMonth($this->user->id);
    foreach($loans as $loan) {
      $budget["expenses"]["loansInterest"] += $this->bankModel->calculateInterest($loan);
    }
    return $budget;
  }
  
  /**
   * Show user's equipment
   * 
   * @return type
   * @throws AuthenticationNeededException
   */
  function equipment() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    return $this->orm->userItems->findEquipment($this->user->id);
  }
  
  /**
   * Equip an item
   * 
   * @param int $id
   * @return void
   * @throws AuthenticationNeededException
   * @throws ItemNotFoundException
   * @throws ItemNotOwnedException
   * @throws ItemNotEquipableException
   * @throws ItemAlreadyWornException
   */
  function equipItem($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $item = $this->orm->userItems->getById($id);
    if(!$item) throw new ItemNotFoundException;
    elseif($item->user->id != $this->user->id) throw new ItemNotOwnedException;
    elseif($item->worn) throw new ItemAlreadyWornException;
    $item->worn = true;
    $this->orm->userItems->persist($item);
    $items = $this->orm->userItems->findByType($this->user->id, $item->item->type);
    foreach($items as $i) {
      if($i->id === $item->id) continue;
      $i->worn = false;
      $this->orm->userItems->persist($i);
    }
    $this->orm->flush();
  }
  
  /**
   * Unequip an item
   * 
   * @param int $id
   * @return void
   * @throws AuthenticationNeededException
   * @throws ItemNotFoundException
   * @throws ItemNotOwnedException
   * @throws ItemNotEquipableException
   * @throws ItemNotWornException
   */
  function unequipItem($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $item = $this->orm->userItems->getById($id);
    if(!$item) throw new ItemNotFoundException;
    elseif($item->user->id != $this->user->id) throw new ItemNotOwnedException;
    elseif($item->item->type === "item") throw new ItemNotEquipableException;
    elseif(!$item->worn) throw new ItemNotWornException;
    $item->worn = false;
    $this->orm->userItems->persistAndFlush($item);
  }
}

class ItemNotOwnedException extends AccessDeniedException {
  
}

class ItemNotEquipableException extends AccessDeniedException {
  
}

class ItemAlreadyWornException extends AccessDeniedException {
  
}

class ItemNotWornException extends AccessDeniedException {
  
}
?>