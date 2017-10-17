<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Group as GroupEntity;

/**
 * Property Model
 *
 * @author Jakub Konečný
 */
class Property {
  /** @var Job*/
  protected $jobModel;
  /** @var Bank */
  protected $bankModel;
  /** @var Taxes */
  protected $taxesModel;
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  
  use \Nette\SmartObject;
  
  public function __construct(Job $jobModel, Bank $bankModel, Taxes $taxesModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->jobModel = $jobModel;
    $this->bankModel = $bankModel;
    $this->taxesModel = $taxesModel;
    $this->orm = $orm;
    $this->user = $user;
  }
  
  protected function calculateBeerProduction(): int {
    $result = 0;
    $beerProduction = $this->orm->beerProduction->findProducedThisMonth($this->user->id);
    foreach($beerProduction as $production) {
      $result += $production->amount * $production->price;
    }
    return $result;
  }
  
  protected function calculateLoansInterest(): int {
    $result = 0;
    $loans = $this->orm->loans->findReturnedThisMonth($this->user->id);
    foreach($loans as $loan) {
      $result += $this->bankModel->calculateInterest($loan);
    }
    return $result;
  }
  
  protected function calculateMembershipFee(): int {
    $result = 0;
    $donations = $this->orm->monasteryDonations->findDonatedThisMonth($this->user->id);
    foreach($donations as $donation) {
      $result += $donation->amount;
    }
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    if($user->guild AND $user->group->path === GroupEntity::PATH_CITY) {
      $result += $user->guildRank->guildFee;
    }
    if($user->order AND $user->group->path === GroupEntity::PATH_TOWER) {
      $result += $user->orderRank->orderFee;
    }
    return $result;
  }
  
  /**
   * Show user's budget
   *
   * @throws AuthenticationNeededException
   */
  public function budget(): array {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $budget = [
      "incomes" => 
        $this->taxesModel->calculateIncome($this->user->id) + [
          "taxes" => 0, "beerProduction" => $this->calculateBeerProduction(),
          ]
      ,
      "expenses" => [
        "incomeTax" => 0,
        "loansInterest" => $this->calculateLoansInterest(),
        "membershipFee" => $this->calculateMembershipFee(),
      ]
    ];
    $budget["expenses"]["incomeTax"] = $this->taxesModel->calculateTax(array_sum($budget["incomes"]));
    $towns = $this->orm->towns->findByOwner($this->user->id);
    foreach($towns as $town) {
      $budget["incomes"]["taxes"] += $this->taxesModel->calculateTownTaxes($town)->taxes;
      if(($town->id === $this->user->identity->town) AND ($town->owner->id === $this->user->id)) {
        $budget["expenses"]["incomeTax"] = 0;
      }
    }
    return $budget;
  }
}
?>