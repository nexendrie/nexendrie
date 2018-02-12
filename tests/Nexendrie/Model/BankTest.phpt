<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert,
    Nexendrie\Orm\Deposit as DepositEntity,
    Nexendrie\Orm\Loan as LoanEntity;

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
    $this->preserveStats(["money"], function() {
      $money = $this->getUserStat("money");
      $this->model->takeLoan(1);
      Assert::same($money + 1, $this->getUserStat("money"));
      $loan = $this->model->getActiveLoan();
      Assert::type(LoanEntity::class, $loan);
      Assert::exception(function() {
        $this->model->takeLoan(1);
      }, CannotTakeMoreLoansException::class);
      /** @var \Nexendrie\Orm\Model $orm */
      $orm = $this->getService(\Nexendrie\Orm\Model::class);
      $orm->loans->removeAndFlush($loan);
    });
  }
  
  public function testReturnLoan() {
    Assert::exception(function() {
      $this->model->returnLoan();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->returnLoan();
    }, NoLoanException::class);
    $this->preserveStats(["money"], function() {
      $money = $this->getUserStat("money");
      $this->model->takeLoan(1);
      $loan = $this->model->getActiveLoan();
      Assert::exception(function() {
        $this->modifyUser(["money" => 0], function() {
          $this->model->returnLoan();
        });
      }, InsufficientFundsException::class);
      $this->model->returnLoan();
      Assert::same($money - 1, $this->getUserStat("money"));
      /** @var \Nexendrie\Orm\Model $orm */
      $orm = $this->getService(\Nexendrie\Orm\Model::class);
      $orm->loans->removeAndFlush($loan);
    });
  }
  
  public function testGetActiveDeposit() {
    Assert::exception(function() {
      $this->model->getActiveDeposit();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::null($this->model->getActiveDeposit());
  }
  
  public function testMaxDeposit() {
    Assert::same(0, $this->model->maxDeposit());
    $this->login();
    Assert::same($this->getUserStat("money"), $this->model->maxDeposit());
  }
  
  public function testOpenDeposit() {
    Assert::exception(function() {
      $this->model->openDeposit(1, time());
    }, TooHighDepositException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->openDeposit($this->getUserStat("money") + 1, time());
    }, TooHighDepositException::class);
    Assert::exception(function() {
      $this->model->openDeposit(1, time() - 1);
    }, InvalidDateException::class);
    $this->preserveStats(["money"], function() {
      $money = $this->getUserStat("money");
      $this->model->openDeposit(1, time());
      Assert::same($money - 1, $this->getUserStat("money"));
      $deposit = $this->model->getActiveDeposit();
      Assert::type(DepositEntity::class, $deposit);
      Assert::exception(function() {
        $this->model->openDeposit(1, time());
      }, CannotOpenMoreDepositAccountsException::class);
      /** @var \Nexendrie\Orm\Model $orm */
      $orm = $this->getService(\Nexendrie\Orm\Model::class);
      $orm->deposits->removeAndFlush($deposit);
    });
  }
  
  public function testCloseDeposit() {
    Assert::exception(function() {
      $this->model->closeDeposit();
    }, AuthenticationNeededException::class);
    $this->login();
    Assert::exception(function() {
      $this->model->closeDeposit();
    }, NoDepositAccountException::class);
    $this->preserveStats(["money"], function() {
      $money = $this->getUserStat("money");
      $this->model->openDeposit(1, time() + 60 * 60 * 24);
      $deposit = $this->model->getActiveDeposit();
      Assert::exception(function() {
        $this->model->closeDeposit();
      }, DepositAccountNotDueException::class);
      $deposit->term = time();
      $this->model->closeDeposit();
      Assert::same($money + 1, $this->getUserStat("money"));
      /** @var \Nexendrie\Orm\Model $orm */
      $orm = $this->getService(\Nexendrie\Orm\Model::class);
      $orm->deposits->removeAndFlush($deposit);
    });
  }
}

$test = new BankTest();
$test->run();
?>