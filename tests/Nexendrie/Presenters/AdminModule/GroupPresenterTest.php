<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class GroupPresenterTest extends \Tester\TestCase
{
    use TAdminPresenter;

    public function testDefault(): void
    {
        $this->defaultChecks(":Admin:Group:default");
    }

    public function testEdit(): void
    {
        $this->defaultChecks(":Admin:Group:edit", ["id" => 1]);
        Assert::exception(function () {
            $this->check(":Admin:Group:edit", ["id" => 5000]);
        }, BadRequestException::class);
    }

    public function testMembers(): void
    {
        $this->defaultChecks(":Admin:Group:members", ["id" => 1]);
        Assert::exception(function () {
            $this->check(":Admin:Group:members", ["id" => 5000]);
        }, BadRequestException::class);
    }
}

$test = new GroupPresenterTest();
$test->run();
