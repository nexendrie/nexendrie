<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

require __DIR__ . "/../../../bootstrap.php";

final class BankPresenterTest extends \Tester\TestCase
{
    use \Nexendrie\Presenters\TPresenter;

    public function testDefault(): void
    {
        $this->checkAction(":Front:Bank:default");
        $this->login();
        $this->checkAction(":Front:Bank:default");
    }

    public function testReturn(): void
    {
        $this->checkRedirect(":Front:Bank:return", "/user/login");
    }

    public function testClose(): void
    {
        $this->checkRedirect(":Front:Bank:close", "/user/login");
    }
}

$test = new BankPresenterTest();
$test->run();
