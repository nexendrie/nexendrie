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
      "incomes" => 
        $this->taxesModel->calculateIncome($this->user->id) + array("taxes" => 0)
      ,
      "expenses" => array(
        "incomeTax" => 0,
        "loansInterest" => 0
    ));
    $budget["expenses"]["incomeTax"] = $this->taxesModel->calculateTax(array_sum($budget["incomes"]));
    $loans = $this->orm->loans->findReturnedThisMonth($this->user->id);
    foreach($loans as $loan) {
      $budget["expenses"]["loansInterest"] += $this->bankModel->calculateInterest($loan);
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
   * @return type
   * @throws AuthenticationNeededException
   */
  function equipment() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    return $this->orm->userItems->findEquipment($this->user->id);
  }
}
?>