<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class MarketPresenterTest extends \Tester\TestCase
{
    use \Nexendrie\Presenters\TPresenter;

    protected function defaultChecks(string $action): void
    {
        $this->checkRedirect($action, "/user/login");
        $this->login();
        $this->checkAction($action);
    }

    public function testDefault(): void
    {
        $this->checkAction(":Front:Market:default");
    }

    public function testShop(): void
    {
        Assert::exception(function () {
            $this->checkAction(":Front:Market:shop", ["id" => 50]);
        }, BadRequestException::class);
        $this->checkAction(":Front:Market:shop", ["id" => 1]);
    }

    public function testBuy(): void
    {
        $this->checkRedirect(":Front:Market:buy", "/user/login", ["id" => 5000]);
    }

    public function testMounts(): void
    {
        $this->defaultChecks(":Front:Market:mounts");
    }

    public function testTowns(): void
    {
        $this->defaultChecks(":Front:Market:towns");
    }
}

$test = new MarketPresenterTest();
$test->run();
