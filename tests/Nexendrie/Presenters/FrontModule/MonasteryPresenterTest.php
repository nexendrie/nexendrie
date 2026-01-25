<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class MonasteryPresenterTest extends \Tester\TestCase
{
    use \Nexendrie\Presenters\TPresenter;

    protected function defaultChecks(string $action, array $params = []): void
    {
        $this->checkRedirect($action, "/user/login", $params);
        $this->login();
        $this->checkRedirect($action, "/", $params);
    }

    public function testDefault(): void
    {
        $this->defaultChecks(":Front:Monastery:default");
        $this->login("Rahym");
        $this->checkAction(":Front:Monastery:default");
    }

    public function testList(): void
    {
        $this->checkAction(":Front:Monastery:list");
    }

    public function testDetail(): void
    {
        Assert::exception(function () {
            $this->check(":Front:Monastery:detail", ["id" => 5000]);
        }, BadRequestException::class);
        $this->checkAction(":Front:Monastery:detail", ["id" => 1]);
    }

    public function testBuild(): void
    {
        $this->defaultChecks(":Front:Monastery:build");
    }

    public function testJoin(): void
    {
        $this->defaultChecks(":Front:Monastery:join", ["id" => 5000]);
        $this->login("bozena");
        Assert::exception(function () {
            $this->check(":Front:Monastery:join", ["id" => 5000]);
        }, BadRequestException::class);
    }

    public function testLeave(): void
    {
        $this->checkRedirect(":Front:Monastery:leave", "/user/login");
        $this->login("Rahym");
        $this->checkRedirect(":Front:Monastery:leave", "/monastery");
    }

    public function testPray(): void
    {
        $this->defaultChecks(":Front:Monastery:pray");
    }

    public function testManage(): void
    {
        $this->defaultChecks(":Front:Monastery:manage");
        $this->login("Rahym");
        $this->checkAction(":Front:Monastery:manage");
    }

    public function testChat(): void
    {
        $this->defaultChecks(":Front:Monastery:chat");
        $this->login("Rahym");
        $this->checkAction(":Front:Monastery:chat");
    }
}

$test = new MonasteryPresenterTest();
$test->run();
