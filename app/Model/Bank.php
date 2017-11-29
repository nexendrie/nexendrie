<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Loan as LoanEntity;

/**
 * Bank Model
 *
 * @author Jakub Konečný
 */
class Bank {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  protected $interest;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user, SettingsRepository $sr) {
    $this->orm = $orm;
    $this->user = $user;
    $this->interest = $sr->settings["fees"]["loanInterest"];
  }
  
  /**
   * Get user's active loan
   *
   * @throws AuthenticationNeededException
   */
  public function getActiveLoan(): ?LoanEntity {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    return $this->orm->loans->getActiveLoan($this->user->id);
  }
  
  public function maxLoan(): int {
    if(!$this->user->isLoggedIn()) {
      return 0;
    }
    $level = $this->user->identity->level;
    if($level < 90) {
      return 70;
    } elseif($level >= 90 AND $level <= 100) {
      return 300;
    } elseif($level > 100 AND $level < 400) {
      return 500;
    } elseif($level === 400) {
      return 700;
    } elseif($level > 400 AND $level < 10000) {
      return 1500;
    }
    return 2000;
  }
  
  /**
   * Calculate interest from a loan
   */
  public function calculateInterest(LoanEntity $loan): int {
    $start = $loan->taken;
    $end = ($loan->returned) ? $loan->returned : time();
    $duration = ($end - $start) / (60 * 60 * 24);
    $interest = (int) ($loan->amount * $loan->interest * $duration / 36500);
    return max([1, $interest]);
  }
  
  /**
   * Take a loan
   *
   * @throws TooHighLoanException
   * @throws CannotTakeMoreLoansException
   */
  public function takeLoan(int $amount): void {
    if($amount > $this->maxLoan()) {
      throw new TooHighLoanException();
    } elseif(!is_null($this->getActiveLoan())) {
      throw new CannotTakeMoreLoansException();
    }
    $loan = new LoanEntity();
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    $loan->user = $user;
    $loan->user->money += $amount;
    $loan->interest = $this->interest;
    $loan->amount = $amount;
    $this->orm->loans->persistAndFlush($loan);
  }
  
  /**
   * Return loan
   *
   * @throws AuthenticationNeededException
   * @throws NoLoanException
   * @throws InsufficientFundsException
   */
  public function returnLoan(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $loan = $this->getActiveLoan();
    if(is_null($loan)) {
      throw new NoLoanException();
    }
    $returnMoney = $loan->amount + $this->calculateInterest($loan);
    if($returnMoney > $loan->user->money) {
      throw new InsufficientFundsException();
    }
    $loan->returned = time();
    $loan->user->money -= $returnMoney;
    $this->orm->loans->persistAndFlush($loan);
  }
}
?>