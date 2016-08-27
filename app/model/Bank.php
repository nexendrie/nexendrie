<?php
namespace Nexendrie\Model;

use Nexendrie\Orm\Loan as LoanEntity;

/**
 * Bank Model
 *
 * @author Jakub Konečný
 */
class Bank extends \Nette\Object {
  /** @var \Nexendrie\Orm\Model */
  protected $orm;
  /** @var \Nette\Security\User */
  protected $user;
  /** @var int */
  protected $interest;
  
  /**
   * @param int $interest
   * @param \Nexendrie\Orm\Model $orm
   * @param \Nette\Security\User $user
   */
  function __construct($interest, \Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
    $this->interest = (int) $interest;
  }
  
  /**
   * Get user's active loan
   * 
   * @return LoanEntity|NULL
   * @throws AuthenticationNeededException
   */
  function getActiveLoan() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    return $this->orm->loans->getActiveLoan($this->user->id);
  }
  
  /**
   * @return int
   */
  function maxLoan() {
    if(!$this->user->isLoggedIn()) return 0;
    $level = $this->user->identity->level;
    if($level < 90) return 70;
    elseif($level >= 90 AND $level <= 100) return 300;
    elseif($level > 100 AND $level < 400) return 500;
    elseif($level === 400) return 700;
    elseif($level > 400 AND $level < 10000) return 1500;
    else return 2000;
  }
  
  /**
   * Calculate interest from a loan
   * 
   * @param LoanEntity $loan
   * @return int
   */
  function calculateInterest(LoanEntity $loan) {
    $start = $loan->taken;
    $end = $loan->returned ? $loan->returned: time();
    $duration = ($end - $start) / (60 * 60 * 24);
    $interest = (int) ($loan->amount * $loan->interest * $duration / 36500);
    return max([1, $interest]);
  }
  
  /**
   * Take a loan
   * 
   * @param int $amount
   * @return void
   * @throws TooHighLoanException
   * @throws CannotTakeMoreLoansException
   */
  function takeLoan($amount) {
    if($amount > $this->maxLoan()) throw new TooHighLoanException;
    elseif($this->getActiveLoan()) throw new CannotTakeMoreLoansException;
    $loan = new LoanEntity;
    $loan->user = $this->orm->users->getById($this->user->id);
    $loan->user->money += $amount;
    $loan->interest = $this->interest;
    $loan->amount = $amount;
    $this->orm->loans->persistAndFlush($loan);
  }
  
  /**
   * Return loan
   * 
   * @return void
   * @throws AuthenticationNeededException
   * @throws NoLoanException
   * @throws InsufficientFundsException
   */
  function returnLoan() {
    if(!$this->user->isLoggedIn()) throw new AuthenticationNeededException;
    $loan = $this->getActiveLoan();
    if(!$loan) throw new NoLoanException;
    $returnMoney = $loan->amount + $this->calculateInterest($loan);
    if($returnMoney > $loan->user->money) throw new InsufficientFundsException;
    $loan->returned = time();
    $loan->user->money -= $returnMoney;
    $this->orm->loans->persistAndFlush($loan);
  }
}

class TooHighLoanException extends AccessDeniedException {
  
}

class CannotTakeMoreLoansException extends AccessDeniedException {
  
}

class NoLoanException extends AccessDeniedException {
  
}
?>