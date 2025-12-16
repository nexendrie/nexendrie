<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Tester\Assert;
use Nette\Application\BadRequestException;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @skip
 */
final class PollPresenterTest extends \Tester\TestCase
{
    use \Nexendrie\Presenters\TPresenter;

    public function testView(): void
    {
        Assert::exception(function () {
            $this->checkAction(":Front:Poll:view", ["id" => 50]);
        }, BadRequestException::class);
        $this->checkAction(":Front:Poll:view", ["id" => 1]);
    }
}

$test = new PollPresenterTest();
$test->run();
