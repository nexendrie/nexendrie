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
   * Show user's budget
   * 
   * @return array
   * @throws AuthenticationNeededException
   */
  function budget() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $budget = array(
      "incomes" => 
        $this->taxesModel->calculateIncome($this->user->id) + array("taxes" => 0, "beerProduction" => 0)
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
    $beerProduction = $this->orm->beerProduction->findProducedThisMonth($this->user->id);
    foreach($beerProduction as $production) {
      $budget["incomes"]["beerProduction"] += $production->amount * $production->price;
    }
    $towns = $this->orm->towns->findByOwner($this->user->id);
    foreach($towns as $town) {
      $budget["incomes"]["taxes"] += $this->taxesModel->calculateTownTaxes($town)->taxes;
      $current = ($town->id === $this->user->identity->town) AND ($town->owner->id === $this->user->id);
      if($current) $budget["expenses"]["incomeTax"] = 0;
    }
    return $budget;
  }
}
?>