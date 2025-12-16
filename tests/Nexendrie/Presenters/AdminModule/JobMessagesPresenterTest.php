<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\AdminModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class JobMessagesPresenterTest extends \Tester\TestCase
{
    use TAdminPresenter;

    public function testList(): void
    {
        $this->defaultChecks(":Admin:JobMessages:list", ["id" => 1]);
        $this->checkForward(":Admin:JobMessages:list", "Admin:Job:notfound", ["id" => 5000]);
    }

    public function testAdd(): void
    {
        $this->defaultChecks(":Admin:JobMessages:add", ["id" => 1]);
        $this->checkForward(":Admin:JobMessages:add", "Admin:Job:notfound", ["id" => 5000]);
    }

    public function testEdit(): void
    {
        $this->defaultChecks(":Admin:JobMessages:edit", ["id" => 1]);
        Assert::exception(function () {
            $this->check(":Admin:JobMessages:edit", ["id" => 5000]);
        }, BadRequestException::class);
    }
}

$test = new JobMessagesPresenterTest();
$test->run();
