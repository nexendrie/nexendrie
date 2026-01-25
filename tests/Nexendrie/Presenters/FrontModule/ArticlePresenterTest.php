<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

final class ArticlePresenterTest extends \Tester\TestCase
{
    use \Nexendrie\Presenters\TPresenter;

    public function testView(): void
    {
        Assert::exception(function () {
            $this->checkAction(":Front:Article:view", ["id" => 50]);
        }, BadRequestException::class);
        $this->checkAction(":Front:Article:view", ["id" => 1]);
        $this->login();
        $this->checkAction(":Front:Article:view", ["id" => 1]);
    }

    public function testSignalReport(): void
    {
        $this->checkSignal(":Front:Article:view", "report", ["id" => 1, "comment" => 50], [], "/article/1");
        $this->login();
        $this->checkSignal(":Front:Article:view", "report", ["id" => 1, "comment" => 50], [], "/article/1");
    }
}

$test = new ArticlePresenterTest();
$test->run();
