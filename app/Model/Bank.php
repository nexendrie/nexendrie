<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Loan as LoanEntity;
use Nexendrie\Orm\Deposit as DepositEntity;
use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\User;

/**
 * Bank Model
 *
 * @author Jakub Konečný
 */
final class Bank {
  protected int $loanInterest;
  protected int $depositInterest;
  
  use \Nette\SmartObject;
  
  public function __construct(private readonly ORM $orm, private readonly \Nette\Security\User $user, SettingsRepository $sr) {
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
    /** @var User $user */
    $user = $this->orm->users->getById($this->user->id);
    return $user->group->maxLoan;
  }
  
  /**
   * Take a loan
   *
   * @throws AuthenticationNeededException
   * @throws TooHighLoanException
   * @throws CannotTakeMoreLoansException
   */
  public function takeLoan(int $amount): void {
    if($amount > $this->maxLoan()) {
      throw new TooHighLoanException("Amount cannot be higher than $amount.", $amount);
    } elseif($this->getActiveLoan() !== null) {
      throw new CannotTakeMoreLoansException();
    }
    $loan = new LoanEntity();
    /** @var User $user */
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
    if($loan === null) {
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
    /** @var User $user */
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
    } elseif($this->getActiveDeposit() !== null) {
      throw new CannotOpenMoreDepositAccountsException();
    }
    $deposit = new DepositEntity();
    /** @var User $user */
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
    if($deposit === null) {
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