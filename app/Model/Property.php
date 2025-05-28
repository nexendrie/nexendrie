<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Group as GroupEntity;

/**
 * Property Model
 *
 * @author Jakub Konečný
 */
final class Property {
  protected Job $jobModel;
  protected Bank $bankModel;
  protected Taxes $taxesModel;
  protected \Nexendrie\Orm\Model $orm;
  protected \Nette\Security\User $user;
  protected SettingsRepository $sr;
  
  use \Nette\SmartObject;
  
  public function __construct(Job $jobModel, Bank $bankModel, Taxes $taxesModel, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user, SettingsRepository $sr) {
    $this->jobModel = $jobModel;
    $this->bankModel = $bankModel;
    $this->taxesModel = $taxesModel;
    $this->orm = $orm;
    $this->user = $user;
    $this->sr = $sr;
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
      $result += $loan->interest;
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
    if($user->guild !== null && $user->guildRank !== null && $user->group->path === GroupEntity::PATH_CITY) {
      $result += $user->guildRank->guildFee;
    }
    if($user->order !== null && $user->orderRank !== null && $user->group->path === GroupEntity::PATH_TOWER) {
      $result += $user->orderRank->orderFee;
    }
    return $result;
  }
  
  protected function calculateDepositInterest(): int {
    $result = 0;
    $deposits = $this->orm->deposits->findDueThisMonth($this->user->id);
    foreach($deposits as $deposit) {
      $result += $deposit->interest;
    }
    return $result;
  }

  protected function calculateMountsMaintenance(): int {
    $mounts = $this->orm->mounts->findAutoFed($this->user->id);
    $result = $mounts->countStored() * $this->sr->settings["fees"]["autoFeedMount"] * 4;
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
          "depositInterest" => $this->calculateDepositInterest(),
          ]
      ,
      "expenses" => [
        "incomeTax" => 0,
        "loansInterest" => $this->calculateLoansInterest(),
        "membershipFee" => $this->calculateMembershipFee(),
        "mountsMaintenance" => $this->calculateMountsMaintenance(),
      ]
    ];
    $budget["expenses"]["incomeTax"] = $this->taxesModel->calculateTax(array_sum($budget["incomes"]));
    $towns = $this->orm->towns->findByOwner($this->user->id);
    foreach($towns as $town) {
      $budget["incomes"]["taxes"] += $this->taxesModel->calculateTownTaxes($town)->taxes;
      if(($town->id === $this->user->identity->town) && ($town->owner->id === $this->user->id)) {
        $budget["expenses"]["incomeTax"] = 0;
      }
    }
    return $budget;
  }
}
?>