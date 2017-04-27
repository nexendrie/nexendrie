<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nexendrie\Orm\Loan;

require __DIR__ . "/../../bootstrap.php";

class BankTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Bank */
  protected $model;
  
  function setUp() {
    $this->model = $this->getService(Bank::class);
  }
  
  function testGetActiveLoan() {
    Assert::exception(function() {
      $this->model->getActiveLoan();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::null($this->model->getActiveLoan());
  }
  
  function testMaxLoan() {
    Assert::same(0, $this->model->maxLoan());
    $this->login();
    Assert::same(2000, $this->model->maxLoan());
  }
  
  function testCalculateInterest() {
    /** @var \Nexendrie\Orm\Model $orm */
    $orm = $this->getService(\Nexendrie\Orm\Model::class);
    $loan = new Loan;
    $orm->loans->attach($loan);
    $loan->amount = 2000;
    $loan->interest = 10;
    $loan->taken = time();
    $interest = $this->model->calculateInterest($loan);
    Assert::type("int", $interest);
    Assert::true($interest >= 1);
    $orm->loans->detach($loan);
  }
  
  function testTakeLoan() {
    $this->login();
    Assert::exception(function() {
      $this->model->takeLoan($this->model->maxLoan() + 1);
    }, TooHighLoanException::class);
  }
  
  function testReturnLoan() {
    Assert::exception(function() {
      $this->model->returnLoan();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->returnLoan();
    }, NoLoanException::class);
  }
}

$test = new BankTest;
$test->run();
?>