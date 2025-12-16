<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class AdventureEnemiesPresenterTest extends \Tester\TestCase
{
    use TAdminPresenter;

    public function testList(): void
    {
        $this->defaultChecks(":Admin:AdventureEnemies:list", ["id" => 1]);
        Assert::exception(function () {
            $this->check(":Admin:AdventureEnemies:list", ["id" => 5000]);
        }, BadRequestException::class);
    }

    public function testAdd(): void
    {
        $this->defaultChecks(":Admin:AdventureEnemies:add", ["id" => 1]);
        Assert::exception(function () {
            $this->check(":Admin:AdventureEnemies:add", ["id" => 5000]);
        }, BadRequestException::class);
    }

    public function testEdit(): void
    {
        $this->defaultChecks(":Admin:AdventureEnemies:edit", ["id" => 1]);
        Assert::exception(function () {
            $this->check(":Admin:AdventureEnemies:edit", ["id" => 5000]);
        }, BadRequestException::class);
    }

    public function testDelete(): void
    {
        $this->checkRedirect(":Admin:AdventureEnemies:delete", "/user/login", ["id" => 1]);
        $this->login("kazimira");
        $this->checkRedirect(":Admin:AdventureEnemies:delete", "/", ["id" => 1]);
        $this->login();
        Assert::exception(function () {
            $this->check(":Admin:AdventureEnemies:delete", ["id" => 5000]);
        }, BadRequestException::class);
    }
}

$test = new AdventureEnemiesPresenterTest();
$test->run();
