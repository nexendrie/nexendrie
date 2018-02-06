<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nexendrie\Orm\Loan;

require __DIR__ . "/../../bootstrap.php";

final class BankTest extends \Tester\TestCase {
  use TUserControl;
  
  /** @var Bank */
  protected $model;
  
  protected function setUp() {
    $this->model = $this->getService(Bank::class);
  }
  
  public function testGetActiveLoan() {
    Assert::exception(function() {
      $this->model->getActiveLoan();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::null($this->model->getActiveLoan());
  }
  
  protected function checkMaxLoan(string $username, int $maxLoan): void {
    $this->login($username);
    Assert::same($maxLoan, $this->model->maxLoan());
  }
  
  public function testMaxLoan() {
    Assert::same(0, $this->model->maxLoan());
    $this->checkMaxLoan("kazimira", 70);
    $this->checkMaxLoan("premysl", 300);
    $this->checkMaxLoan("jakub", 500);
    $this->checkMaxLoan("svetlana", 700);
    $this->checkMaxLoan("Rahym", 1500);
    $this->checkMaxLoan("admin", 2000);
  }
  
  public function testCalculateInterest() {
    /** @var \Nexendrie\Orm\Model $orm */
    $orm = $this->getService(\Nexendrie\Orm\Model::class);
    $loan = new Loan();
    $orm->loans->attach($loan);
    $loan->amount = 2000;
    $loan->interest = 10;
    $loan->taken = time();
    $interest = $this->model->calculateInterest($loan);
    Assert::type("int", $interest);
    Assert::true($interest >= 1);
    $orm->loans->removeAndFlush($loan);
  }
  
  public function testTakeLoan() {
    $this->login();
    Assert::exception(function() {
      $this->model->takeLoan($this->model->maxLoan() + 1);
    }, TooHighLoanException::class);
  }
  
  public function testReturnLoan() {
    Assert::exception(function() {
      $this->model->returnLoan();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->returnLoan();
    }, NoLoanException::class);
  }
}

$test = new BankTest();
$test->run();
?>