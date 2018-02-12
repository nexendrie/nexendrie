<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;

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