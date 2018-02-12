<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Loan as LoanEntity,
    Nexendrie\Orm\Deposit as DepositEntity;

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
  protected $loanInterest;
  /** @var int */
  protected $depositInterest;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user, SettingsRepository $sr) {
    $this->orm = $orm;
    $this->user = $user;
    $this->loanInterest = $sr->settings["fees"]["loanInterest"];
    $this->depositInterest = $sr->settings["fees"]["depositInterest"];
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
    $loan->interestRate = $this->loanInterest;
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
    $returnMoney = $loan->amount + $loan->interest;
    if($returnMoney > $loan->user->money) {
      throw new InsufficientFundsException();
    }
    $loan->returned = time();
    $loan->user->money -= $returnMoney;
    $this->orm->loans->persistAndFlush($loan);
  }
  
  /**
   * Get user's active deposit account
   *
   * @throws AuthenticationNeededException
   */
  public function getActiveDeposit(): ?DepositEntity {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    return $this->orm->deposits->getActiveDeposit($this->user->id);
  }
  
  public function maxDeposit(): int {
    if(!$this->user->isLoggedIn()) {
      return 0;
    }
    $user = $this->orm->users->getById($this->user->id);
    return $user->money;
  }
  
  /**
   * @throws TooHighDepositException
   * @throws InvalidDateException
   * @throws CannotOpenMoreDepositAccountsException
   */
  public function openDeposit(int $amount, int $term): void {
    if($amount > $this->maxDeposit()) {
      throw new TooHighDepositException();
    } elseif($term < time()) {
      throw new InvalidDateException();
    } elseif(!is_null($this->getActiveDeposit())) {
      throw new CannotOpenMoreDepositAccountsException();
    }
    $deposit = new DepositEntity();
    /** @var \Nexendrie\Orm\User $user */
    $user = $this->orm->users->getById($this->user->id);
    $deposit->user = $user;
    $deposit->amount = $amount;
    $deposit->user->money -= $amount;
    $deposit->interestRate = $this->depositInterest;
    $deposit->term = $term;
    $this->orm->deposits->persistAndFlush($deposit);
  }
  
  /**
   * @throws AuthenticationNeededException
   * @throws NoDepositAccountException
   * @throws DepositAccountNotDueException
   */
  public function closeDeposit(): void {
    if(!$this->user->isLoggedIn()) {
      throw new AuthenticationNeededException();
    }
    $deposit = $this->getActiveDeposit();
    if(is_null($deposit)) {
      throw new NoDepositAccountException();
    } elseif(!$deposit->due) {
      throw new DepositAccountNotDueException();
    }
    $deposit->closed = true;
    $deposit->user->money += $deposit->amount + $deposit->interest;
    $this->orm->deposits->persistAndFlush($deposit);
  }
}
?>