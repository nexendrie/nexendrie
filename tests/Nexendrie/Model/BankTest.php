<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Tester\Assert;
use Nexendrie\Orm\Deposit as DepositEntity;
use Nexendrie\Orm\Loan as LoanEntity;

require __DIR__ . "/../../bootstrap.php";

final class BankTest extends \Tester\TestCase
{
    use TUserControl;

    protected Bank $model;

    protected function setUp(): void
    {
        $this->model = $this->getService(Bank::class); // @phpstan-ignore assign.propertyType
    }

    public function testGetActiveLoan(): void
    {
        Assert::exception(function () {
            $this->model->getActiveLoan();
        }, AuthenticationNeededException::class);
        $this->login();
        Assert::null($this->model->getActiveLoan());
    }

    public function testMaxLoan(): void
    {
        Assert::same(0, $this->model->maxLoan());
        $this->login();
        Assert::notSame(0, $this->model->maxLoan());
    }

    public function testTakeLoan(): void
    {
        $this->login();
        Assert::exception(function () {
            $this->model->takeLoan(100000000);
        }, TooHighLoanException::class);
        $this->preserveStats(["money"], function () {
            $money = $this->getUserStat("money");
            $this->model->takeLoan(1);
            Assert::same($money + 1, $this->getUserStat("money"));
            /** @var LoanEntity $loan */
            $loan = $this->model->getActiveLoan();
            Assert::type(LoanEntity::class, $loan);
            Assert::exception(function () {
                $this->model->takeLoan(1);
            }, CannotTakeMoreLoansException::class);
            /** @var \Nexendrie\Orm\Model $orm */
            $orm = $this->getService(\Nexendrie\Orm\Model::class);
            $orm->loans->removeAndFlush($loan);
        });
    }

    public function testReturnLoan(): void
    {
        Assert::exception(function () {
            $this->model->returnLoan();
        }, AuthenticationNeededException::class);
        $this->login();
        Assert::exception(function () {
            $this->model->returnLoan();
        }, NoLoanException::class);
        $this->preserveStats(["money"], function () {
            $money = $this->getUserStat("money");
            $this->model->takeLoan(1);
            /** @var LoanEntity $loan */
            $loan = $this->model->getActiveLoan();
            Assert::exception(function () {
                $this->modifyUser(["money" => 0], function () {
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

    public function testGetActiveDeposit(): void
    {
        Assert::exception(function () {
            $this->model->getActiveDeposit();
        }, AuthenticationNeededException::class);
        $this->login();
        Assert::null($this->model->getActiveDeposit());
    }

    public function testMaxDeposit(): void
    {
        Assert::same(0, $this->model->maxDeposit());
        $this->login();
        Assert::same($this->getUserStat("money"), $this->model->maxDeposit());
    }

    public function testOpenDeposit(): void
    {
        Assert::exception(function () {
            $this->model->openDeposit(1, time());
        }, TooHighDepositException::class);
        $this->login();
        Assert::exception(function () {
            $this->model->openDeposit($this->getUserStat("money") + 1, time());
        }, TooHighDepositException::class);
        Assert::exception(function () {
            $this->model->openDeposit(1, time() - 1);
        }, InvalidDateException::class);
        $this->preserveStats(["money"], function () {
            $money = $this->getUserStat("money");
            $this->model->openDeposit(1, time());
            Assert::same($money - 1, $this->getUserStat("money"));
            /** @var DepositEntity $deposit */
            $deposit = $this->model->getActiveDeposit();
            Assert::type(DepositEntity::class, $deposit);
            Assert::exception(function () {
                $this->model->openDeposit(1, time());
            }, CannotOpenMoreDepositAccountsException::class);
            /** @var \Nexendrie\Orm\Model $orm */
            $orm = $this->getService(\Nexendrie\Orm\Model::class);
            $orm->deposits->removeAndFlush($deposit);
        });
    }

    public function testCloseDeposit(): void
    {
        Assert::exception(function () {
            $this->model->closeDeposit();
        }, AuthenticationNeededException::class);
        $this->login();
        Assert::exception(function () {
            $this->model->closeDeposit();
        }, NoDepositAccountException::class);
        $this->preserveStats(["money"], function () {
            $money = $this->getUserStat("money");
            $this->model->openDeposit(1, time() + 60 * 60 * 24);
            /** @var DepositEntity $deposit */
            $deposit = $this->model->getActiveDeposit();
            Assert::exception(function () {
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
