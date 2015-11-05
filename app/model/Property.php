<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\UserItem as UserItemEntity,
    Nexendrie\Orm\Item as ItemEntity;

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
  /** @var \Nexendrie\Model\Taxes */
  protected $taxesModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  function __construct(\Nexendrie\Model\Job $jobModel, \Nexendrie\Model\Bank $bankModel, \Nexendrie\Model\Taxes $taxesModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->jobModel = $jobModel;
    $this->bankModel = $bankModel;
    $this->taxesModel = $taxesModel;
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
    $return["items"] = $user->items->get()->findBy(array("this->item->type" => ItemEntity::getCommonTypes()));
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
      "incomes" => 
        $this->taxesModel->calculateIncome($this->user->id) + array("taxes" => 0)
      ,
      "expenses" => array(
        "incomeTax" => 0,
        "loansInterest" => 0,
        "monasteryDonations" => 0
    ));
    $budget["expenses"]["incomeTax"] = $this->taxesModel->calculateTax(array_sum($budget["incomes"]));
    $loans = $this->orm->loans->findReturnedThisMonth($this->user->id);
    foreach($loans as $loan) {
      $budget["expenses"]["loansInterest"] += $this->bankModel->calculateInterest($loan);
    }
    $donations = $this->orm->monasteryDonations->findDonatedThisMonth($this->user->id);
    foreach($donations as $donation) {
      $budget["expenses"]["monasteryDonations"] += $donation->amount;
    }
    $towns = $this->orm->towns->findByOwner($this->user->id);
    foreach($towns as $town) {
      $budget["incomes"]["taxes"] += $this->taxesModel->calculateTownTaxes($town)->taxes;
      $current = ($town->id === $this->user->identity->town) AND ($town->owner->id === $this->user->id);
      if($current) $budget["incomes"]["taxes"] += $budget["expenses"]["incomeTax"];
    }
    return $budget;
  }
  
  /**
   * Show user's equipment
   * 
   * @return UserItemEntity[]
   * @throws AuthenticationNeededException
   */
  function equipment() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    return $this->orm->userItems->findEquipment($this->user->id);
  }
  
  /**
   * Show user's potions
   * 
   * @return UserItemEntity[]
   * @throws AuthenticationNeededException
   */
  function potions() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    return $this->orm->userItems->findByType($this->user->id, "potion");
  }
  
  /**
   * Drink a potion
   * 
   * @param int $id
   * @return int
   * @throws AuthenticationNeededException
   * @throws ItemNotFoundException
   * @throws ItemNotOwnedException
   * @throws ItemNotDrinkableException
   * @throws HealingNotNeeded
   */
  function drinkPotion($id) {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $item = $this->orm->userItems->getById($id);
    if(!$item) throw new ItemNotFoundException;
    elseif($item->user->id != $this->user->id) throw new ItemNotOwnedException;
    elseif($item->item->type != "potion") throw new ItemNotDrinkableException;
    if($item->user->life >= $item->user->maxLife) throw new HealingNotNeeded;
    $item->amount -= 1;
    $life = $item->item->strength;
    if($item->amount < 1) {
      $user = $this->orm->users->getById($this->user->id);
      $this->orm->userItems->remove($item);
      $user->life += $life;
      $this->orm->users->persist($user);
      $this->orm->flush();
    } else {
      $item->user->life += $item->item->strength;
      $this->orm->userItems->persistAndFlush($item);
    }
    return $life;
  }
}

class ItemNotDrinkableException extends AccessDeniedException {

}

class HealingNotNeeded extends AccessDeniedException {

}
?>