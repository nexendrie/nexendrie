<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class ItemSetPresenterTest extends \Tester\TestCase
{
    use TAdminPresenter;

    public function testNew(): void
    {
        $this->defaultChecks(":Admin:ItemSet:new");
    }

    public function testEdit(): void
    {
        $this->defaultChecks(":Admin:ItemSet:edit", ["id" => 1]);
        Assert::exception(function () {
            $this->check(":Admin:ItemSet:edit", ["id" => 5000]);
        }, BadRequestException::class);
    }

    public function testDelete(): void
    {
        $this->checkRedirect(":Admin:ItemSet:delete", "/user/login", ["id" => 1]);
        $this->login("kazimira");
        $this->checkRedirect(":Admin:ItemSet:delete", "/", ["id" => 1]);
        $this->login();
        Assert::exception(function () {
            $this->check(":Admin:ItemSet:delete", ["id" => 5000]);
        }, BadRequestException::class);
    }
}

$test = new ItemSetPresenterTest();
$test->run();
